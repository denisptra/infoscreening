<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PortalFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully_with_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create(['name' => 'Artikel', 'slug' => 'artikel', 'color' => '#3A07BB']);
        $article = Article::create([
            'user_id' => $admin->id,
            'title' => 'Sinema Alternatif Indonesia Terkini',
            'slug' => 'sinema-alternatif-indonesia-terkini',
            'excerpt' => 'Catatan penting gerakan film mandiri.',
            'content' => 'Isi artikel lengkap tentang ekosistem perfilman Indonesia.',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now()->subDay(),
            'views' => 125,
        ]);
        $article->categories()->sync([$category->id]);

        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('Sinema Alternatif Indonesia Terkini');
        $response->assertSee('#3A07BB', false);
    }

    public function test_articles_detail_page_increments_views(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $article = Article::create([
            'user_id' => $admin->id,
            'title' => 'Wawancara Eksklusif Sineas',
            'slug' => 'wawancara-eksklusif-sineas',
            'content' => 'Wawancara mendalam seputar produksi film.',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'views' => 10,
        ]);

        $response = $this->get('/artikel/'.$article->slug);
        $response->assertStatus(200);
        $response->assertSee('Wawancara Eksklusif Sineas');

        $this->assertDatabaseHas('articles', [
            'id' => $article->id,
            'views' => 11,
        ]);
    }

    public function test_events_kalender_page_loads(): void
    {
        Event::create([
            'title' => 'Festival Film Alternatif 2026',
            'slug' => 'festival-film-alternatif-2026',
            'type' => 'Festival',
            'city' => 'Jakarta',
            'venue' => 'Kineforum',
            'starts_at' => now()->addDays(5),
            'is_published' => true,
        ]);

        $response = $this->get('/events');
        $response->assertStatus(200);
        $response->assertSee('Festival Film Alternatif 2026');
    }

    public function test_user_can_submit_contribution(): void
    {
        $response = $this->post('/berkontribusi', [
            'name' => 'Budi Utomo',
            'email' => 'budi@gmail.com',
            'type' => 'Artikel',
            'title' => 'Liputan Pemutaran Layar Tanjab',
            'message' => 'Ini adalah naskah lengkap liputan pemutaran layar tanjab di pesisir utara.',
            'link' => 'https://drive.google.com/test',
        ]);

        $response->assertRedirect('/berkontribusi');
        $this->assertDatabaseHas('contributions', [
            'email' => 'budi@gmail.com',
            'title' => 'Liputan Pemutaran Layar Tanjab',
            'status' => 'pending',
        ]);
    }

    public function test_directory_and_shop_pages_load(): void
    {
        Expert::create([
            'name' => 'Dr. Filmolog',
            'specialty' => 'Pengkaji Sinema',
            'city' => 'Yogyakarta',
            'is_published' => true,
        ]);

        Product::create([
            'name' => 'Zine Sinema Mandiri',
            'slug' => 'zine-sinema-mandiri',
            'price' => 50000,
            'is_available' => true,
        ]);

        $this->get('/rupa-jasa/tenaga-ahli')->assertStatus(200)->assertSee('Dr. Filmolog');
        $this->get('/toko')->assertStatus(200)->assertSee('Zine Sinema Mandiri');
        $this->get('/tentang-kami')->assertStatus(200)->assertSee('Tentang Kami');
    }

    public function test_admin_dashboard_requires_admin_role(): void
    {
        // Guest redirected to Filament admin login
        $this->get('/admin')->assertRedirect('/admin/login');

        // Normal non-admin user is forbidden
        $user = User::factory()->create(['is_admin' => false]);
        $this->actingAs($user)->get('/admin')->assertForbidden();

        // Admin user can access Filament dashboard
        $admin = User::factory()->create(['is_admin' => true]);
        $this->actingAs($admin)->get('/admin')->assertStatus(200);

        // Admin can access Filament Article and Event resources
        $this->actingAs($admin)->get('/admin/articles')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/events')->assertStatus(200);
    }

    public function test_admin_media_library_can_list_and_upload(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        Storage::fake('public');

        $file = UploadedFile::fake()->create('test-poster.jpg', 150, 'image/jpeg');

        // Upload via AJAX
        $response = $this->actingAs($admin)->postJson('/strapi/media', [
            'file' => $file,
            'caption' => 'Poster Festival 2026',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('media', [
            'name' => 'test-poster.jpg',
            'caption' => 'Poster Festival 2026',
        ]);

        // Access media library view
        $this->actingAs($admin)->get('/strapi/media')
            ->assertStatus(200)
            ->assertSee('Media Library')
            ->assertSee('test-poster.jpg');
    }

    public function test_user_dashboard_does_not_escalate_regular_users_to_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/');
        $this->assertFalse($user->fresh()->is_admin);

        $this->actingAs($user)->get('/strapi')->assertForbidden();
    }

    public function test_admin_content_type_builder_and_settings_accessible(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/strapi/content-types')
            ->assertStatus(200)
            ->assertSee('Content-Type Builder')
            ->assertSee('Article');

        $this->actingAs($admin)->get('/strapi/settings')
            ->assertStatus(200)
            ->assertSee('Strapi CMS Settings')
            ->assertSee('API Tokens');

        $this->actingAs($admin)->post('/strapi/settings/tokens', [
            'name' => 'Automated Test Token',
            'type' => 'read-only',
            'description' => 'Token for automated tests',
        ])->assertRedirect('/strapi/settings?tab=tokens');
    }
}
