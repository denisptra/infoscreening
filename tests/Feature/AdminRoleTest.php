<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AdminRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_editorial_admin_can_publish_articles_and_manage_content_collections_but_cannot_manage_site_settings(): void
    {
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $otherArticle = Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Artikel Editor',
            'slug' => 'artikel-editor',
            'content' => 'Draf artikel editor.',
            'status' => 'draft',
        ]);

        $this->actingAs($admin)->get('/strapi/settings')->assertForbidden();
        $this->actingAs($admin)->get('/strapi/content-types')->assertForbidden();
        $this->actingAs($admin)->get('/strapi/events')->assertOk();
        $this->actingAs($admin)->get('/strapi/categories')->assertOk();
        $this->actingAs($admin)->get('/strapi/tags')->assertOk();
        $this->actingAs($admin)->get('/strapi/media')->assertOk();
        $this->actingAs($admin)->get(route('strapi.articles.edit', $otherArticle))->assertOk();

        $response = $this->actingAs($admin)->post('/strapi/articles', [
            'title' => 'Berita Redaksi',
            'content' => 'Isi berita untuk pengujian.',
            'status' => 'published',
        ]);

        $response->assertRedirect('/strapi/articles');
        $this->assertDatabaseHas('articles', [
            'user_id' => $admin->id,
            'title' => 'Berita Redaksi',
            'status' => 'published',
        ]);
    }

    public function test_editor_can_only_manage_own_drafts_and_cannot_publish_them(): void
    {
        $editor = User::factory()->editor()->create();
        $otherEditor = User::factory()->editor()->create();
        $ownDraft = Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Draf Milik Editor',
            'slug' => 'draf-milik-editor',
            'content' => 'Naskah draf.',
            'status' => 'draft',
        ]);
        $otherDraft = Article::query()->create([
            'user_id' => $otherEditor->id,
            'title' => 'Draf Editor Lain',
            'slug' => 'draf-editor-lain',
            'content' => 'Naskah privat.',
            'status' => 'draft',
        ]);
        $publishedArticle = Article::query()->create([
            'user_id' => $editor->id,
            'title' => 'Artikel Terbit Editor',
            'slug' => 'artikel-terbit-editor',
            'content' => 'Artikel yang sudah terbit.',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $this->actingAs($editor)->get('/strapi/articles')
            ->assertOk()
            ->assertSee($ownDraft->title)
            ->assertDontSee($otherDraft->title);
        $this->actingAs($editor)->get('/strapi/articles/'.$otherDraft->slug.'/edit')->assertForbidden();
        $this->actingAs($editor)->get('/strapi/articles/'.$publishedArticle->slug.'/edit')->assertForbidden();

        $response = $this->actingAs($editor)->post('/strapi/articles', [
            'title' => 'Draf Baru Penulis',
            'content' => 'Naskah baru.',
            'status' => 'published',
            'published_at' => now()->addDay()->toDateTimeString(),
            'is_featured' => true,
        ]);

        $response->assertRedirect('/strapi/articles');
        $this->assertDatabaseHas('articles', [
            'user_id' => $editor->id,
            'title' => 'Draf Baru Penulis',
            'status' => 'draft',
            'published_at' => null,
            'is_featured' => false,
        ]);
    }

    public function test_super_admin_can_assign_admin_and_editor_roles(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->create();
        $editor = User::factory()->create();

        $this->actingAs($superAdmin)->patch(route('strapi.settings.users.role', $admin), [
            'role' => 'admin',
        ])->assertRedirect(route('strapi.settings.index', ['tab' => 'users']));

        $this->actingAs($superAdmin)->patch(route('strapi.settings.users.role', $editor), [
            'role' => 'editor',
        ])->assertRedirect(route('strapi.settings.index', ['tab' => 'users']));

        $this->assertSame(UserRole::Admin, $admin->fresh()->role);
        $this->assertTrue($admin->fresh()->is_admin);
        $this->assertSame(UserRole::Editor, $editor->fresh()->role);
        $this->assertFalse($editor->fresh()->is_admin);
    }

    public function test_existing_author_accounts_are_migrated_to_editor_role(): void
    {
        $editor = User::factory()->editor()->create();
        DB::table('users')->where('id', $editor->id)->update(['role' => 'author']);

        $migration = require database_path('migrations/2026_11_04_063246_rename_author_role_to_editor_role.php');
        $migration->up();

        $this->assertSame(UserRole::Editor, $editor->fresh()->role);
    }

    public function test_editor_cannot_access_administrative_content_or_user_management(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/strapi/categories')->assertForbidden();
        $this->actingAs($editor)->get('/strapi/settings')->assertForbidden();
        $this->actingAs($editor)->get('/admin')->assertForbidden();
    }

    public function test_super_admin_can_manage_navigation_and_site_settings(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->get('/strapi/settings')->assertOk();
        $this->actingAs($superAdmin)->get('/strapi/navigation')->assertOk();
    }

    public function test_bootstrap_command_promotes_an_existing_account_to_super_admin(): void
    {
        $user = User::factory()->create();

        $exitCode = Artisan::call('app:promote-super-admin', [
            'email' => $user->email,
        ]);

        $this->assertSame(0, $exitCode);
        $this->assertSame(UserRole::SuperAdmin, $user->fresh()->role);
        $this->assertTrue($user->fresh()->is_admin);
    }

    public function test_all_cms_roles_can_create_articles_with_base64_encoded_content(): void
    {
        $users = [
            User::factory()->superAdmin()->create(),
            User::factory()->admin()->create(),
            User::factory()->editor()->create(),
        ];

        foreach ($users as $user) {
            $content = '<p>Isi artikel dengan <strong>HTML</strong> dan <em>format</em>.</p>';

            $response = $this->actingAs($user)->post('/strapi/articles', [
                'title' => 'Artikel '.$user->role->value,
                'content_is_encoded' => '1',
                'content_encoded' => base64_encode($content),
                'status' => 'published',
            ]);

            $response->assertRedirect('/strapi/articles');
            $this->assertDatabaseHas('articles', [
                'user_id' => $user->id,
                'content' => $content,
            ]);
        }
    }

    public function test_editor_can_access_media_library_for_article_images(): void
    {
        $editor = User::factory()->editor()->create();

        $this->actingAs($editor)->get('/strapi/media')->assertOk();
    }
}
