<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\NavigationItem;
use App\Models\Tag;
use App\Models\User;
use Database\Seeders\DefaultTaxonomySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NavigationItemTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_create_a_dropdown_menu_and_publish_it_on_the_site(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post('/strapi/navigation', [
            'label' => 'Program Baru',
            'location' => 'header',
            'url' => '/program',
            'sort_order' => 80,
            'is_active' => true,
        ])->assertRedirect('/strapi/navigation');

        $parent = NavigationItem::query()->where('label', 'Program Baru')->firstOrFail();

        $this->actingAs($superAdmin)->post('/strapi/navigation', [
            'label' => 'Festival Mendatang',
            'location' => 'header',
            'parent_id' => $parent->id,
            'url' => '/events',
            'sort_order' => 10,
            'is_active' => true,
        ])->assertRedirect('/strapi/navigation');

        $this->assertDatabaseHas('navigation_items', [
            'parent_id' => $parent->id,
            'label' => 'Festival Mendatang',
            'url' => '/events',
            'is_active' => true,
        ]);

        $this->get('/')->assertOk()->assertSee('Festival Mendatang');
    }

    public function test_super_admin_can_customize_footer_text(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->put('/strapi/appearance/footer', [
            'footer_about' => 'Keterangan footer yang disesuaikan.',
            'footer_copyright' => 'Infoscreening Studio',
        ])->assertRedirect('/strapi/navigation');

        $this->assertDatabaseHas('site_settings', [
            'key' => 'footer_about',
            'value' => 'Keterangan footer yang disesuaikan.',
        ]);
        $this->get('/')->assertOk()->assertSee('Keterangan footer yang disesuaikan.');
    }

    public function test_default_taxonomy_is_seeded_and_linked_from_the_article_dropdown(): void
    {
        Category::query()->create([
            'name' => 'Kategori Lain',
            'slug' => 'kategori-lain',
        ]);
        $this->seed(DefaultTaxonomySeeder::class);

        $this->assertDatabaseHas('categories', [
            'name' => 'Berita Film',
            'slug' => 'berita-film',
        ]);
        $this->assertDatabaseHas('tags', [
            'name' => 'Film Pendek',
            'slug' => 'film-pendek',
        ]);

        $articleMenu = NavigationItem::query()
            ->where('location', 'header')
            ->where('label', 'Artikel')
            ->firstOrFail();
        $footerMenu = NavigationItem::query()
            ->where('location', 'footer')
            ->where('label', 'Rubrikasi Artikel')
            ->firstOrFail();

        $categories = Category::query()
            ->whereIn('slug', ['berita-film', 'festival-film', 'wawancara-sineas', 'kritik-film', 'pemutaran-film'])
            ->get();
        $this->assertCount(5, $categories);

        foreach ($categories as $category) {
            $this->assertDatabaseHas('navigation_items', [
                'parent_id' => $articleMenu->id,
                'label' => $category->name,
                'url' => '/kategori/'.$category->slug,
            ]);
            $this->assertDatabaseHas('navigation_items', [
                'parent_id' => $footerMenu->id,
                'label' => $category->name,
                'url' => '/kategori/'.$category->slug,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('Berita Film')
            ->assertSee('/kategori/berita-film')
            ->assertSee('#Film Pendek');

        $this->get(route('categories.show', ['category' => 'berita-film']))->assertOk();

        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin)
            ->get(route('strapi.dashboard'))
            ->assertOk()
            ->assertSee('Rubrik Artikel')
            ->assertSee('Berita Film')
            ->assertSee('Topik Pilihan')
            ->assertSee('#Film Pendek')
            ->assertDontSee('Kategori Lain');
    }

    public function test_default_taxonomy_seeder_preserves_custom_category_and_menu_labels(): void
    {
        $this->seed(DefaultTaxonomySeeder::class);

        $category = Category::query()->where('slug', 'berita-film')->firstOrFail();
        $category->update(['name' => 'Warta Perfilman']);

        $menuItem = NavigationItem::query()
            ->where('location', 'header')
            ->where('url', '/kategori/berita-film')
            ->firstOrFail();
        $menuItem->update(['label' => 'Berita Pilihan']);

        $this->seed(DefaultTaxonomySeeder::class);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Warta Perfilman',
            'slug' => 'berita-film',
        ]);
        $this->assertDatabaseHas('navigation_items', [
            'id' => $menuItem->id,
            'label' => 'Berita Pilihan',
            'url' => '/kategori/berita-film',
        ]);
        $this->assertSame(1, Category::query()->where('slug', 'berita-film')->count());
    }

    public function test_footer_combines_static_links_with_dynamic_categories_tags_and_cms_menu(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::query()->create([
            'name' => 'Festival Film',
            'slug' => 'festival-film',
            'color' => '#3A07BB',
        ]);
        $tag = Tag::query()->create([
            'name' => 'Sinema Rabu',
            'slug' => 'sinema-rabu',
        ]);
        $article = Article::query()->create([
            'user_id' => $admin->id,
            'title' => 'Arsip Festival',
            'slug' => 'arsip-festival',
            'content' => 'Artikel untuk menghubungkan topik footer.',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);
        $article->categories()->attach($category);
        $article->tags()->attach($tag);

        $footerGroup = NavigationItem::query()->create([
            'location' => 'footer',
            'label' => 'Ruang Komunitas',
            'sort_order' => 1,
            'is_active' => true,
        ]);
        NavigationItem::query()->create([
            'parent_id' => $footerGroup->id,
            'location' => 'footer',
            'label' => 'Kirim Naskah',
            'url' => '/berkontribusi',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Rubrikasi Artikel')
            ->assertSee('Festival Film')
            ->assertSee(route('categories.show', ['category' => $category]), false)
            ->assertSee('Ekosistem Sinema')
            ->assertSee('Kirim Naskah')
            ->assertSee('Topik Pilihan')
            ->assertSee('#Sinema Rabu')
            ->assertSee(route('tags.show', ['tag' => $tag]), false)
            ->assertSee('aria-label="Facebook"', false)
            ->assertSee('Kebijakan Privasi');
    }

    public function test_navigation_rejects_javascript_urls(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)->post('/strapi/navigation', [
            'label' => 'Tautan Tidak Aman',
            'location' => 'header',
            'url' => 'javascript:alert(1)',
            'sort_order' => 80,
        ])->assertSessionHasErrors(['url']);

        $this->assertDatabaseMissing('navigation_items', [
            'label' => 'Tautan Tidak Aman',
        ]);
    }

    public function test_editorial_admin_can_access_and_manage_navigation(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/strapi/navigation')->assertOk();

        $this->actingAs($admin)->post('/strapi/navigation', [
            'label' => 'Kategori Khusus',
            'location' => 'header',
            'url' => '/category/film-indonesia',
            'sort_order' => 90,
            'is_active' => true,
        ])->assertRedirect('/strapi/navigation');

        // URL /category/ should automatically normalize to /kategori/
        $this->assertDatabaseHas('navigation_items', [
            'label' => 'Kategori Khusus',
            'url' => '/kategori/film-indonesia',
        ]);
    }

    public function test_legacy_category_url_redirects_301_to_kategori(): void
    {
        $category = Category::query()->create([
            'name' => 'Film Indonesia',
            'slug' => 'film-indonesia',
        ]);

        $this->get('/category/film-indonesia')
            ->assertStatus(301)
            ->assertRedirect(route('categories.show', ['category' => $category]));

        $this->get('/category')
            ->assertStatus(301)
            ->assertRedirect('/kategori');
    }

    public function test_normalizing_urls_converts_category_to_kategori(): void
    {
        $admin = User::factory()->admin()->create();

        $item = NavigationItem::query()->create([
            'label' => 'Test Legacy Category',
            'location' => 'header',
            'url' => '/category/sinema-baru',
            'sort_order' => 95,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->post('/strapi/navigation/normalize-urls')
            ->assertRedirect('/strapi/navigation');

        $this->assertSame('/kategori/sinema-baru', $item->fresh()->url);
    }
}
