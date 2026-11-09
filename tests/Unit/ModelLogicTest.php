<?php

namespace Tests\Unit;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Expert;
use App\Models\Product;
use App\Models\User;
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
}
