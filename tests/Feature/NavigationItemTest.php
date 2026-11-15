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

    public function test_site_navbar_is_fixed_and_article_dropdown_lists_all_categories_with_correct_links(): void
    {
        $catA = Category::query()->create(['name' => 'Berita Film', 'slug' => 'berita-film', 'color' => '#ba233b']);
        $catB = Category::query()->create(['name' => 'Festival Film', 'slug' => 'festival-film', 'color' => '#7344ff']);
        $catC = Category::query()->create(['name' => 'Wawancara Sineas', 'slug' => 'wawancara-sineas', 'color' => '#16807a']);

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Beranda')
            ->assertSee('Artikel')
            ->assertSee('/artikel', false)
            ->assertSee('Kalender')
            ->assertSee('/events', false)
            ->assertSee('Rupa Jasa')
            ->assertSee('Tentang')
            ->assertSee('/tentang-kami', false)
            ->assertSee('Kontak')
            ->assertSee('/kontak', false);

        foreach ([$catA, $catB, $catC] as $category) {
            $response->assertSee($category->name)
                ->assertSee(route('categories.show', ['category' => $category], false), false);
        }
    }

    public function test_default_taxonomy_is_seeded_and_shown_in_navbar_dropdown_and_dashboard(): void
    {
        Category::query()->create([
            'name' => 'Kategori Lain',
            'slug' => 'kategori-lain',
        ]);
        $this->seed(DefaultTaxonomySeeder::class);

        $this->assertDatabaseHas('categories', ['name' => 'Berita Film', 'slug' => 'berita-film']);
        $this->assertDatabaseHas('tags', ['name' => 'Film Pendek', 'slug' => 'film-pendek']);

        $categories = Category::query()
            ->whereIn('slug', ['berita-film', 'festival-film', 'wawancara-sineas', 'kritik-film', 'pemutaran-film'])
            ->get();
        $this->assertCount(5, $categories);

        $home = $this->get('/');
        $home->assertOk()
            ->assertSee('Berita Film')
            ->assertSee('/kategori/berita-film', false)
            ->assertSee('#Film Pendek');

        foreach ($categories as $category) {
            $home->assertSee('/kategori/'.$category->slug, false);
        }

        $this->get(route('categories.show', ['category' => 'berita-film']))->assertOk();

        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin)
            ->get(route('strapi.dashboard'))
            ->assertOk()
            ->assertSee('Rubrik Artikel')
            ->assertSee('Berita Film')
            ->assertSee('Topik Pilihan')
            ->assertSee('#Film Pendek');
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

    public function test_footer_shows_fixed_structure_with_categories_and_tags(): void
    {
        $admin = User::factory()->superAdmin()->create();
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

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Rubrikasi Artikel')
            ->assertSee('Festival Film')
            ->assertSee(route('categories.show', ['category' => $category]), false)
            ->assertSee('Ekosistem Sinema')
            ->assertSee('Kirim Liputan / Naskah')
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

    public function test_editorial_admin_cannot_access_navigation_and_gets_404(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/strapi/navigation')->assertNotFound();
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
        $admin = User::factory()->superAdmin()->create();

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
