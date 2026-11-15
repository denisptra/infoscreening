<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
use App\Models\NavigationItem;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComprehensiveCrudAndRolesTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_access_matrix_across_all_cms_routes(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();
        $admin = User::factory()->admin()->create();
        $editor = User::factory()->editor()->create();
        $member = User::factory()->create();

        $editorRoutes = [
            'strapi.dashboard',
            'strapi.articles.index',
            'strapi.media.index',
        ];

        $adminRoutes = [
            'strapi.categories.index',
            'strapi.tags.index',
            'strapi.events.index',
            'strapi.experts.index',
            'strapi.products.index',
            'strapi.contributions.index',
        ];

        $superAdminOnlyRoutes = [
            'strapi.navigation.index',
            'strapi.settings.index',
            'strapi.content-types.index',
        ];

        $allCmsRoutes = array_merge($editorRoutes, $adminRoutes, $superAdminOnlyRoutes);

        // Guest redirected to login
        foreach ($allCmsRoutes as $routeName) {
            $this->get(route($routeName))->assertRedirect(route('login'));
        }

        // Member gets 404 on all /strapi routes and redirected on /dashboard
        $this->actingAs($member)->get('/dashboard')->assertRedirect(route('home'));
        foreach ($allCmsRoutes as $routeName) {
            $this->actingAs($member)->get(route($routeName))->assertNotFound();
        }

        // Editor gets 200 on editor routes, 404 on admin & super_admin routes
        foreach ($editorRoutes as $routeName) {
            $this->actingAs($editor)->get(route($routeName))->assertOk();
        }
        foreach (array_merge($adminRoutes, $superAdminOnlyRoutes) as $routeName) {
            $this->actingAs($editor)->get(route($routeName))->assertNotFound();
        }

        // Admin gets 200 on editor + admin routes, 404 on super_admin-only routes
        foreach (array_merge($editorRoutes, $adminRoutes) as $routeName) {
            $this->actingAs($admin)->get(route($routeName))->assertOk();
        }
        foreach ($superAdminOnlyRoutes as $routeName) {
            $this->actingAs($admin)->get(route($routeName))->assertNotFound();
        }

        // Super Admin gets 200 on all CMS routes
        foreach ($allCmsRoutes as $routeName) {
            $this->actingAs($superAdmin)->get(route($routeName))->assertOk();
        }
    }

    public function test_articles_full_crud_and_published_category_visibility(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::query()->create([
            'name' => 'Berita Sinema',
            'slug' => 'berita-sinema',
            'color' => '#3A07BB',
        ]);
        $tag = Tag::query()->create([
            'name' => 'Dokumenter',
            'slug' => 'dokumenter',
        ]);

        // Index & Create
        $this->actingAs($admin)->get(route('strapi.articles.index'))->assertOk();
        $this->actingAs($admin)->get(route('strapi.articles.create'))->assertOk();

        // Store published article WITHOUT published_at -> defaults to now() and appears in category & /artikel
        $this->actingAs($admin)->post(route('strapi.articles.store'), [
            'title' => 'Pemutaran Film Alternatif Jakarta',
            'excerpt' => 'Ringkasan pemutaran film.',
            'content' => '<p>Isi lengkap berita pemutaran film alternatif.</p>',
            'status' => 'published',
            'categories' => [$category->id],
            'tags' => [$tag->id],
        ])->assertRedirect(route('strapi.articles.index'));

        $article = Article::query()->where('slug', 'pemutaran-film-alternatif-jakarta')->firstOrFail();
        $this->assertNotNull($article->published_at);
        $this->assertTrue($article->categories->contains($category));

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee('Pemutaran Film Alternatif Jakarta');
        $this->get(route('articles.index'))
            ->assertOk()
            ->assertSee('Pemutaran Film Alternatif Jakarta');

        // Store draft article -> MUST NOT appear in category or /artikel
        $this->actingAs($admin)->post(route('strapi.articles.store'), [
            'title' => 'Artikel Draf Rahasia',
            'content' => '<p>Belum terbit.</p>',
            'status' => 'draft',
            'categories' => [$category->id],
        ])->assertRedirect(route('strapi.articles.index'));

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertDontSee('Artikel Draf Rahasia');
        $this->get(route('articles.index'))
            ->assertOk()
            ->assertDontSee('Artikel Draf Rahasia');

        // Edit & Update with datetime-local in Asia/Jakarta
        $this->actingAs($admin)->get(route('strapi.articles.edit', $article))->assertOk();
        $localTime = now('Asia/Jakarta')->subMinutes(5)->format('Y-m-d\TH:i');
        $this->actingAs($admin)->put(route('strapi.articles.update', $article), [
            'title' => 'Pemutaran Film Alternatif Jakarta Updated',
            'slug' => $article->slug,
            'content' => '<p>Konten diperbarui.</p>',
            'status' => 'published',
            'published_at' => $localTime,
            'categories' => [$category->id],
        ])->assertRedirect(route('strapi.articles.index'));

        $this->get(route('categories.show', $category))
            ->assertOk()
            ->assertSee('Pemutaran Film Alternatif Jakarta Updated');

        // Destroy
        $this->actingAs($admin)->delete(route('strapi.articles.destroy', $article))
            ->assertRedirect(route('strapi.articles.index'));
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }

    public function test_categories_and_tags_full_crud(): void
    {
        $admin = User::factory()->admin()->create();

        // Categories CRUD
        $this->actingAs($admin)->get(route('strapi.categories.create'))->assertOk();
        $this->actingAs($admin)->post(route('strapi.categories.store'), [
            'name' => 'Esai Film',
            'description' => 'Kumpulan esai sinema.',
            'color' => '#3A07BB',
        ])->assertRedirect(route('strapi.categories.index'));

        $category = Category::query()->where('slug', 'esai-film')->firstOrFail();
        $this->actingAs($admin)->get(route('strapi.categories.edit', $category))->assertOk();
        $this->actingAs($admin)->put(route('strapi.categories.update', $category), [
            'name' => 'Esai Sinema',
            'slug' => 'esai-film',
            'color' => '#190457',
        ])->assertRedirect(route('strapi.categories.index'));
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'name' => 'Esai Sinema']);

        $this->actingAs($admin)->delete(route('strapi.categories.destroy', $category))
            ->assertRedirect(route('strapi.categories.index'));
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);

        // Tags CRUD
        $this->actingAs($admin)->get(route('strapi.tags.create'))->assertOk();
        $this->actingAs($admin)->post(route('strapi.tags.store'), [
            'name' => 'Sinema Independen',
            'description' => 'Tag film independen.',
        ])->assertRedirect(route('strapi.tags.index'));

        $tag = Tag::query()->where('slug', 'sinema-independen')->firstOrFail();
        $this->actingAs($admin)->get(route('strapi.tags.edit', $tag))->assertOk();
        $this->actingAs($admin)->put(route('strapi.tags.update', $tag), [
            'name' => 'Sinema Indie',
            'slug' => 'sinema-independen',
        ])->assertRedirect(route('strapi.tags.index'));
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => 'Sinema Indie']);

        $this->actingAs($admin)->delete(route('strapi.tags.destroy', $tag))
            ->assertRedirect(route('strapi.tags.index'));
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_events_experts_and_products_full_crud(): void
    {
        $admin = User::factory()->admin()->create();

        // Events CRUD
        $this->actingAs($admin)->get(route('strapi.events.create'))->assertOk();
        $this->actingAs($admin)->post(route('strapi.events.store'), [
            'title' => 'Festival Film Nusantara',
            'type' => 'Festival',
            'venue' => 'Taman Ismail Marzuki',
            'city' => 'Jakarta',
            'starts_at' => now()->addDays(5)->format('Y-m-d\TH:i'),
            'is_published' => '1',
        ])->assertRedirect(route('strapi.events.index'));

        $event = Event::query()->where('title', 'Festival Film Nusantara')->firstOrFail();
        $this->actingAs($admin)->get(route('strapi.events.edit', $event))->assertOk();
        $this->actingAs($admin)->put(route('strapi.events.update', $event), [
            'title' => 'Festival Film Nusantara 2026',
            'type' => 'Pemutaran',
            'starts_at' => now()->addDays(6)->format('Y-m-d\TH:i'),
            'is_published' => '1',
        ])->assertRedirect(route('strapi.events.index'));
        $this->assertDatabaseHas('events', ['id' => $event->id, 'title' => 'Festival Film Nusantara 2026']);

        $this->actingAs($admin)->delete(route('strapi.events.destroy', $event))
            ->assertRedirect(route('strapi.events.index'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);

        // Experts CRUD
        $this->actingAs($admin)->get(route('strapi.experts.create'))->assertOk();
        $this->actingAs($admin)->post(route('strapi.experts.store'), [
            'name' => 'Budi Santoso',
            'specialty' => 'Sinematografi',
            'city' => 'Yogyakarta',
            'is_published' => '1',
        ])->assertRedirect(route('strapi.experts.index'));

        $expert = Expert::query()->where('name', 'Budi Santoso')->firstOrFail();
        $this->actingAs($admin)->get(route('strapi.experts.edit', $expert))->assertOk();
        $this->actingAs($admin)->put(route('strapi.experts.update', $expert), [
            'name' => 'Budi Santoso Updated',
            'specialty' => 'Penyuntingan Film',
            'is_published' => '1',
        ])->assertRedirect(route('strapi.experts.index'));
        $this->assertDatabaseHas('experts', ['id' => $expert->id, 'name' => 'Budi Santoso Updated']);

        $this->actingAs($admin)->delete(route('strapi.experts.destroy', $expert))
            ->assertRedirect(route('strapi.experts.index'));
        $this->assertDatabaseMissing('experts', ['id' => $expert->id]);

        // Products CRUD
        $this->actingAs($admin)->get(route('strapi.products.create'))->assertOk();
        $this->actingAs($admin)->post(route('strapi.products.store'), [
            'name' => 'Zine Sinema Edisi 1',
            'description' => 'Zine cetak terbatas.',
            'price' => 75000,
            'is_available' => '1',
        ])->assertRedirect(route('strapi.products.index'));

        $product = Product::query()->where('name', 'Zine Sinema Edisi 1')->firstOrFail();
        $this->actingAs($admin)->get(route('strapi.products.edit', $product))->assertOk();
        $this->actingAs($admin)->put(route('strapi.products.update', $product), [
            'name' => 'Zine Sinema Edisi 1 Revisi',
            'price' => 85000,
            'is_available' => '1',
        ])->assertRedirect(route('strapi.products.index'));
        $this->assertDatabaseHas('products', ['id' => $product->id, 'price' => 85000]);

        $this->actingAs($admin)->delete(route('strapi.products.destroy', $product))
            ->assertRedirect(route('strapi.products.index'));
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_contributions_navigation_and_media_crud(): void
    {
        Storage::fake('public');
        $admin = User::factory()->admin()->create();

        // Contributions (index/show/update/destroy)
        $contribution = Contribution::query()->create([
            'name' => 'Sinta',
            'email' => 'sinta@example.test',
            'type' => 'Artikel',
            'title' => 'Liputan Bioskop Keliling',
            'message' => 'Detail liputan.',
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->get(route('strapi.contributions.index'))->assertOk();
        $this->actingAs($admin)->get(route('strapi.contributions.show', $contribution))->assertOk();
        $this->actingAs($admin)->put(route('strapi.contributions.update', $contribution), [
            'status' => 'approved',
        ])->assertRedirect(route('strapi.contributions.show', $contribution));
        $this->assertDatabaseHas('contributions', ['id' => $contribution->id, 'status' => 'approved']);

        $this->actingAs($admin)->delete(route('strapi.contributions.destroy', $contribution))
            ->assertRedirect(route('strapi.contributions.index'));
        $this->assertDatabaseMissing('contributions', ['id' => $contribution->id]);

        // NavigationItems CRUD (Super Admin only)
        $superAdmin = User::factory()->superAdmin()->create();
        $this->actingAs($superAdmin)->get(route('strapi.navigation.create'))->assertOk();
        $this->actingAs($superAdmin)->post(route('strapi.navigation.store'), [
            'label' => 'Arsip Khusus',
            'location' => 'header',
            'url' => '/artikel',
            'sort_order' => 90,
            'is_active' => '1',
        ])->assertRedirect(route('strapi.navigation.index'));

        $navItem = NavigationItem::query()->where('label', 'Arsip Khusus')->firstOrFail();
        $this->actingAs($superAdmin)->get(route('strapi.navigation.edit', $navItem))->assertOk();
        $this->actingAs($superAdmin)->put(route('strapi.navigation.update', $navItem), [
            'label' => 'Arsip Pilihan',
            'location' => 'header',
            'url' => '/kategori',
            'sort_order' => 95,
            'is_active' => '1',
        ])->assertRedirect(route('strapi.navigation.index'));
        $this->assertDatabaseHas('navigation_items', ['id' => $navItem->id, 'label' => 'Arsip Pilihan']);

        $this->actingAs($superAdmin)->delete(route('strapi.navigation.destroy', $navItem))
            ->assertRedirect(route('strapi.navigation.index'));
        $this->assertDatabaseMissing('navigation_items', ['id' => $navItem->id]);

        // Media CRUD (index/store/destroy)
        $this->actingAs($admin)->get(route('strapi.media.index'))->assertOk();
        $this->actingAs($admin)->post(route('strapi.media.store'), [
            'file' => UploadedFile::fake()->create('poster.pdf', 100, 'application/pdf'),
            'caption' => 'Poster Festival',
        ])->assertRedirect();

        $media = Media::query()->where('caption', 'Poster Festival')->firstOrFail();
        $this->actingAs($admin)->delete(route('strapi.media.destroy', $media))
            ->assertRedirect();
        $this->assertDatabaseMissing('media', ['id' => $media->id]);
    }

    public function test_password_reset_flow_works_end_to_end(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'email' => 'sineas@example.test',
            'password' => Hash::make('OldPassword123!'),
        ]);

        // GET /forgot-password
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('Lupa Password');

        // POST /forgot-password
        $this->post(route('password.email'), [
            'email' => $user->email,
        ])->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });
        $this->assertNotNull($token);

        // GET /reset-password/{token}
        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk()
            ->assertSee('Atur Ulang Password');

        // POST /reset-password
        $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'NewPassword123!',
            'password_confirmation' => 'NewPassword123!',
        ])->assertRedirect(route('login'));

        $user->refresh();
        $this->assertTrue(Hash::check('NewPassword123!', $user->password));

        // Login with new password
        $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'NewPassword123!',
        ])->assertRedirect();
        $this->assertAuthenticatedAs($user);
    }
}
