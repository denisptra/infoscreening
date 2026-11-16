<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Expert;
use App\Models\Media;
use App\Models\Product;
use App\Models\User;
use App\Support\MediaUrl;
use Filament\Panel;
use Tests\TestCase;

class ModelLogicTest extends TestCase
{
    public function test_article_scopes_and_attributes(): void
    {
        $article = new Article([
            'title' => 'Judul Uji Coba Sinema',
            'content' => 'Satu dua tiga empat lima enam tujuh delapan sembilan sepuluh.',
            'featured_image' => 'https://example.com/poster.jpg',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'views' => 150,
        ]);

        $this->assertSame('https://example.com/poster.jpg', $article->image_url);
        $this->assertSame('slug', $article->getRouteKeyName());
    }

    public function test_product_formatted_price_and_image(): void
    {
        $product = new Product([
            'name' => 'Koleksi Zine Sinema',
            'price' => 75000,
            'image' => 'https://example.com/zine.jpg',
        ]);

        $this->assertSame('Rp 75.000', $product->formatted_price);
        $this->assertSame('https://example.com/zine.jpg', $product->image_url);
    }

    public function test_expert_photo_url_accessor(): void
    {
        $expert = new Expert([
            'name' => 'Konsultan Film',
            'photo' => 'https://example.com/photo.jpg',
        ]);

        $this->assertSame('https://example.com/photo.jpg', $expert->photo_url);
    }

    public function test_user_panel_access_rbac(): void
    {
        $panel = \Mockery::mock(Panel::class);

        $adminUser = new User;
        $adminUser->forceFill(['role' => UserRole::Admin]);
        $this->assertFalse($adminUser->canAccessPanel($panel));

        $editorUser = new User;
        $editorUser->forceFill(['role' => UserRole::Editor]);
        $this->assertFalse($editorUser->canAccessPanel($panel));

        $superAdmin = new User;
        $superAdmin->forceFill(['role' => UserRole::SuperAdmin]);
        $this->assertTrue($superAdmin->canAccessPanel($panel));
    }

    public function test_image_url_normalizes_localhost_and_relative_storage_paths(): void
    {
        $article = new Article([
            'featured_image' => 'http://localhost:8000/storage/uploads/2026/11/poster.jpg',
        ]);
        $this->assertSame('/storage/uploads/2026/11/poster.jpg', $article->image_url);

        $media = new Media([
            'file_path' => 'uploads/2026/11/still.jpg',
        ]);
        $this->assertSame('/storage/uploads/2026/11/still.jpg', $media->url);

        $loopbackMedia = new Media([
            'file_path' => 'http://127.0.0.1:8000/storage/articles/cover.jpg',
        ]);
        $this->assertSame('/storage/articles/cover.jpg', $loopbackMedia->url);

        $lanArticle = new Article([
            'featured_image' => 'http://192.168.1.25:8000/storage/uploads/2026/11/lan.jpg',
            'content' => '<p>Lihat <img src="http://localhost:8000/storage/articles/inline.jpg"> dan <img src="https://infoscreening.id/wp-content/uploads/2026/10/ext.jpg"></p>',
        ]);
        $this->assertSame('/storage/uploads/2026/11/lan.jpg', $lanArticle->image_url);
        $this->assertSame(
            '<p>Lihat <img src="/storage/articles/inline.jpg"> dan <img src="https://infoscreening.id/wp-content/uploads/2026/10/ext.jpg"></p>',
            $lanArticle->rendered_content
        );

        $category = new Category([
            'name' => 'Festival',
            'hero_image' => 'storage/categories/hero.jpg',
        ]);
        $this->assertSame('/storage/categories/hero.jpg', $category->hero_image_url);
        $this->assertSame('uploads/2026/11/lan.jpg', MediaUrl::normalizeForStorage('http://[::1]:8000/storage/uploads/2026/11/lan.jpg'));
        $this->assertSame('https://example.com/poster.jpg', MediaUrl::normalizeForStorage('https://example.com/poster.jpg'));
        $this->assertTrue(MediaUrl::isLocalOrPrivateHost('10.0.0.12'));
        $this->assertFalse(MediaUrl::isLocalOrPrivateHost('next.infoscreening.id'));
    }
}
