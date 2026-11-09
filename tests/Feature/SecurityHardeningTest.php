<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SecurityHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_model_protects_role_and_admin_flag_from_mass_assignment(): void
    {
        $user = User::create([
            'name' => 'Attacker',
            'email' => 'attacker@example.com',
            'password' => 'secret123',
            'is_admin' => true,
            'role' => UserRole::SuperAdmin->value,
        ]);

        $this->assertFalse((bool) $user->fresh()->is_admin, 'is_admin must not be mass-assignable on User creation.');
        $this->assertSame(UserRole::Member, $user->fresh()->role);

        $user->fill([
            'is_admin' => true,
            'role' => UserRole::SuperAdmin->value,
        ]);
        $user->save();

        $this->assertFalse((bool) $user->fresh()->is_admin, 'is_admin must not be mass-assignable via fill().');
        $this->assertSame(UserRole::Member, $user->fresh()->role);
    }

    public function test_security_headers_are_present_on_all_responses(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('X-XSS-Protection', '0');
        $response->assertHeader('Cross-Origin-Opener-Policy', 'same-origin');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $this->assertStringContainsString("default-src 'self'", $response->headers->get('Content-Security-Policy'));
    }

    public function test_direct_image_upload_rejects_disallowed_mime_types(): void
    {
        $admin = User::factory()->admin()->create();

        Storage::fake('public');

        $maliciousFile = UploadedFile::fake()->create('exploit.php', 10, 'application/x-php');

        $response = $this->actingAs($admin)->post('/strapi/articles', [
            'title' => 'Test Article',
            'content' => 'Sample article content body for testing.',
            'status' => 'draft',
            'direct_featured_image' => $maliciousFile,
        ]);

        $response->assertSessionHasErrors(['direct_featured_image']);
        $this->assertDatabaseMissing('articles', ['title' => 'Test Article']);
    }

    public function test_path_traversal_in_image_resolution_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post('/strapi/articles', [
            'title' => 'Traversal Attack Article',
            'content' => 'Attempting path traversal exploit.',
            'status' => 'draft',
            'featured_image' => '../../.env',
        ]);

        $response->assertSessionHasErrors(['featured_image']);
        $this->assertDatabaseMissing('articles', ['title' => 'Traversal Attack Article']);
    }

    public function test_search_sanitizes_wildcards_and_limits_length(): void
    {
        $response = $this->get('/cari?q='.urlencode('%_%_special_wildcard_%_%'));
        $response->assertStatus(200);

        // Very long string over 100 characters must fail validation gracefully
        $overlongQuery = str_repeat('a', 120);
        $overlongResponse = $this->get('/cari?q='.$overlongQuery);
        $overlongResponse->assertSessionHasErrors(['q']);
    }

    public function test_regular_users_cannot_access_strapi_or_filament_admin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/strapi')->assertForbidden();
        $this->actingAs($user)->get('/admin')->assertForbidden();
        $this->actingAs($user)->get('/strapi/settings')->assertForbidden();
    }
}
