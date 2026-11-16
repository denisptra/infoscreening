<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
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
        $admin = User::factory()->admin()->create();
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
        $admin = User::factory()->admin()->create();
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

    public function test_article_detail_preserves_legacy_author_attribution(): void
    {
        $admin = User::factory()->admin()->create();
        $article = Article::create([
            'user_id' => $admin->id,
            'title' => 'Arsip Artikel Lama',
            'slug' => 'arsip-artikel-lama',
            'content' => 'Isi artikel arsip.',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'legacy_author_name' => 'Penulis WordPress',
        ]);

        $this->get('/artikel/'.$article->slug)
            ->assertOk()
            ->assertSee('Penulis WordPress');
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

    public function test_super_admin_uses_filament_while_admin_uses_editorial_panel(): void
    {
        $this->get('/admin')->assertRedirect('/admin/login');

        $user = User::factory()->create();
        $this->actingAs($user)->get('/admin')->assertNotFound();

        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->get('/admin')->assertNotFound();
        $this->actingAs($admin)->get('/strapi')->assertOk();
        $this->actingAs($admin)->get('/strapi/articles')->assertOk();
        $this->actingAs($admin)->get('/strapi/events')->assertOk();
        $this->actingAs($admin)->get('/strapi/settings')->assertNotFound();

        $editor = User::factory()->editor()->create();
        $this->actingAs($editor)->get('/strapi')->assertOk();
        $this->actingAs($editor)->get('/strapi/articles')->assertOk();
        $this->actingAs($editor)->get('/strapi/events')->assertNotFound();

        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin)->get('/admin')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/articles')->assertOk();
        $this->actingAs($superAdmin)->get('/admin/events')->assertOk();
    }

    public function test_admin_media_library_can_list_and_upload(): void
    {
        $admin = User::factory()->superAdmin()->create();

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
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertRedirect('/');
        $this->assertFalse($user->fresh()->is_admin);

        $this->actingAs($user)->get('/strapi')->assertNotFound();
    }

    public function test_admin_content_type_builder_and_settings_accessible(): void
    {
        $admin = User::factory()->superAdmin()->create();

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

    public function test_public_storage_fallback_route_serves_uploaded_images_without_session_cookies(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('uploads/2026/11/poster.jpg', 'fake-image-bytes');

        $response = $this->get('/storage/uploads/2026/11/poster.jpg');

        $response->assertOk();
        $this->assertStringContainsString('public', (string) $response->headers->get('Cache-Control'));
        $this->assertEmpty($response->headers->getCookies());

        $this->get('/storage/../../.env')->assertNotFound();
    }

    public function test_article_upload_and_cross_device_urls_are_normalized_and_shared_media_preserved(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        $file = UploadedFile::fake()->createWithContent('still.png', $pngBytes);
        $uploadResponse = $this->actingAs($admin)->postJson('/strapi/media', [
            'file' => $file,
        ])->assertOk()->assertJsonPath('success', true);

        $filePath = $uploadResponse->json('media.file_path');
        $mediaUrl = $uploadResponse->json('media.url');
        $this->assertStringStartsWith('/storage/', $mediaUrl);
        Storage::disk('public')->assertExists($filePath);

        $this->actingAs($admin)->post('/strapi/articles', [
            'title' => 'Artikel Gambar Lintas Perangkat',
            'content' => '<p>Foto: <img src="http://localhost:8000/storage/'.$filePath.'"></p>',
            'status' => 'published',
            'featured_image' => 'http://localhost:8000'.$mediaUrl,
        ])->assertRedirect('/strapi/articles');

        $article = Article::query()->where('slug', 'artikel-gambar-lintas-perangkat')->firstOrFail();
        $this->assertSame($filePath, $article->featured_image);
        $this->assertSame('/storage/'.$filePath, $article->image_url);
        $this->assertStringNotContainsString('localhost:8000', $article->content);

        $this->get('/artikel/'.$article->slug)
            ->assertOk()
            ->assertSee('/storage/'.$filePath, false)
            ->assertDontSee('http://localhost:8000/storage/', false);

        // Removing image from article keeps shared Media Library file intact
        $this->actingAs($admin)->put(route('strapi.articles.update', $article), [
            'title' => $article->title,
            'slug' => $article->slug,
            'content' => $article->content,
            'status' => 'published',
            'remove_featured_image' => '1',
        ])->assertRedirect('/strapi/articles');

        $this->assertNull($article->fresh()->featured_image);
        Storage::disk('public')->assertExists($filePath);
        $this->assertDatabaseHas('media', ['file_path' => $filePath]);
    }

    public function test_media_library_sync_unifies_content_backup_images_storage_files_and_wp_attachments(): void
    {
        Storage::fake('public');
        $pngBytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=');
        Storage::disk('public')->put('articles/orphan-cover.png', $pngBytes);

        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Artikel Backup WP',
            'slug' => 'artikel-backup-wp',
            'content' => '<p>Body <img src="https://infoscreening.id/wp-content/uploads/2026/10/inline.jpg"></p>',
            'featured_image' => 'https://infoscreening.id/wp-content/uploads/2026/10/hero.jpg',
            'status' => 'published',
            'published_at' => now()->subDays(5),
        ]);

        Article::create([
            'user_id' => $admin->id,
            'title' => 'Artikel Missing Local',
            'slug' => 'artikel-missing-local',
            'content' => 'Konten',
            'featured_image' => 'media/2026/11/nonexistent.jpg',
            'status' => 'draft',
        ]);

        Event::create([
            'title' => 'Event Backup WP',
            'slug' => 'event-backup-wp',
            'type' => 'Festival',
            'starts_at' => now()->addDays(2),
            'image' => 'https://infoscreening.id/wp-content/uploads/2026/09/poster.png',
            'is_published' => true,
        ]);

        $wpDumpPath = tempnam(sys_get_temp_dir(), 'wp-media-dump-');
        file_put_contents($wpDumpPath, <<<'SQL'
CREATE TABLE `wp_posts` (
  `ID` bigint(20) unsigned NOT NULL,
  `post_date` datetime NOT NULL,
  `post_date_gmt` datetime NOT NULL,
  `post_title` text NOT NULL,
  `post_excerpt` text NOT NULL,
  `guid` varchar(255) NOT NULL,
  `post_type` varchar(20) NOT NULL,
  `post_mime_type` varchar(100) NOT NULL
);
INSERT INTO `wp_posts` (`ID`, `post_date`, `post_date_gmt`, `post_title`, `post_excerpt`, `guid`, `post_type`, `post_mime_type`) VALUES
(901, '2025-08-01 10:00:00', '2025-08-01 03:00:00', 'Poster Arsip Festival', '', 'https://infoscreening.id/wp-content/uploads/2025/08/arsip-festival.jpg', 'attachment', 'image/jpeg');
SQL);

        try {
            $this->artisan('app:sync-media-library', ['--wp-dump' => $wpDumpPath])
                ->assertSuccessful();

            // Idempotent second run creates no duplicates
            $this->artisan('app:sync-media-library', ['--wp-dump' => $wpDumpPath])
                ->assertSuccessful();
        } finally {
            @unlink($wpDumpPath);
        }

        $this->assertDatabaseHas('media', ['file_path' => 'https://infoscreening.id/wp-content/uploads/2026/10/hero.jpg']);
        $this->assertDatabaseHas('media', ['file_path' => 'https://infoscreening.id/wp-content/uploads/2026/10/inline.jpg']);
        $this->assertDatabaseHas('media', ['file_path' => 'https://infoscreening.id/wp-content/uploads/2026/09/poster.png']);
        $this->assertDatabaseHas('media', ['file_path' => 'https://infoscreening.id/wp-content/uploads/2025/08/arsip-festival.jpg']);
        $this->assertDatabaseHas('media', ['file_path' => 'articles/orphan-cover.png']);
        $this->assertDatabaseMissing('media', ['file_path' => 'media/2026/11/nonexistent.jpg']);
        $this->assertSame(5, Media::count());

        $externalMedia = Media::query()->where('file_path', 'https://infoscreening.id/wp-content/uploads/2026/10/hero.jpg')->firstOrFail();
        $this->assertSame('Eksternal', $externalMedia->formatted_size);

        $this->actingAs($editor)->post(route('strapi.media.sync'))->assertNotFound();
        $this->actingAs($admin)->post(route('strapi.media.sync'))->assertRedirect(route('strapi.media.index'));

        $this->actingAs($admin)
            ->getJson(route('strapi.media.index'))
            ->assertOk()
            ->assertJsonPath('success', true)
            ->assertJsonPath('total', 5)
            ->assertJsonPath('has_more', false);
    }
}
