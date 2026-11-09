<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Contribution;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNotificationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_sees_pending_contributions_and_editor_drafts(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $editor = User::factory()->editor()->create();

        Contribution::query()->create([
            'name' => 'Nara',
            'email' => 'nara@example.test',
            'type' => 'Artikel',
            'title' => 'Kontribusi Festival Film',
            'message' => 'Liputan festival film.',
            'status' => 'pending',
        ]);
        Contribution::query()->create([
            'name' => 'Raka',
            'email' => 'raka@example.test',
            'type' => 'Artikel',
            'title' => 'Kontribusi yang sudah diproses',
            'message' => 'Sudah ditinjau.',
            'status' => 'approved',
        ]);
        Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Draf Festival Editor',
            'slug' => 'draf-festival-editor',
            'content' => 'Naskah draf.',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($superAdmin)->get(route('strapi.dashboard'));

        $response->assertOk()
            ->assertSee('aria-label="Buka notifikasi"', false)
            ->assertSee('Kontribusi Festival Film')
            ->assertSee('Draf Festival Editor')
            ->assertDontSee('Kontribusi yang sudah diproses')
            ->assertSee('fixed inset-y-0 left-0', false)
            ->assertSee('fixed top-0 right-0 left-0 lg:left-64', false)
            ->assertSee('lg:ml-64', false);
    }

    public function test_admin_sees_editor_drafts_but_not_own_or_published_articles(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Draf untuk Admin',
            'slug' => 'draf-untuk-admin',
            'content' => 'Naskah yang perlu ditinjau.',
            'status' => 'draft',
        ]);
        Article::query()->create([
            'user_id' => $admin->id,
            'title' => 'Draf Pribadi Admin',
            'slug' => 'draf-pribadi-admin',
            'content' => 'Naskah pribadi.',
            'status' => 'draft',
        ]);
        Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Artikel Editor Terbit',
            'slug' => 'artikel-editor-terbit',
            'content' => 'Artikel yang sudah terbit.',
            'status' => 'published',
        ]);

        $response = $this->actingAs($admin)->get(route('strapi.dashboard'));

        $response->assertOk()
            ->assertSee('Draf untuk Admin')
            ->assertSee('Draf editor perlu ditinjau')
            ->assertSee('1 tugas');
    }

    public function test_editor_sees_an_empty_notification_state_instead_of_other_users_content(): void
    {
        $editor = User::factory()->editor()->create();

        Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Draf Milik Editor',
            'slug' => 'draf-milik-editor',
            'content' => 'Naskah draf.',
            'status' => 'draft',
        ]);

        $response = $this->actingAs($editor)->get(route('strapi.dashboard'));

        $response->assertOk()
            ->assertSee('Belum ada tugas baru untuk Anda.')
            ->assertDontSee('Draf editor perlu ditinjau');
    }
}
