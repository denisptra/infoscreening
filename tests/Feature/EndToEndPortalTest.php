<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EndToEndPortalTest extends TestCase
{
    use RefreshDatabase;

    public function test_complete_visitor_browsing_journey(): void
    {
        $admin = User::factory()->admin()->create(['name' => 'Redaktur Utama']);
        $category = Category::create(['name' => 'Wawancara', 'slug' => 'wawancara', 'color' => '#8338EC']);
        $tag = Tag::create(['name' => 'Sinema Alternatif', 'slug' => 'sinema-alternatif']);

        $article = Article::create([
            'user_id' => $admin->id,
            'title' => 'Evolusi Festival Film Indonesia Kontemporer',
            'slug' => 'evolusi-festival-film-indonesia-kontemporer',
            'excerpt' => 'Catatan kuratorial seputar arah baru sinema mandiri.',
            'content' => 'Teks naskah lengkap artikel kurasi mendalam.',
            'status' => 'published',
            'is_featured' => true,
            'published_at' => now()->subHours(2),
            'views' => 45,
        ]);
        $article->categories()->sync([$category->id]);
        $article->tags()->sync([$tag->id]);

        $event = Event::create([
            'title' => 'Pemutaran Layar Komunitas 2026',
            'slug' => 'pemutaran-layar-komunitas-2026',
            'type' => 'Pemutaran',
            'city' => 'Bandung',
            'venue' => 'Kolektif Cinema',
            'starts_at' => now()->addDays(3),
            'is_published' => true,
        ]);

        $expert = Expert::create([
            'name' => 'Sari Filmologis',
            'specialty' => 'Kurator & Programmer',
            'city' => 'Jakarta',
            'bio' => 'Pengkaji sejarah sinema Asia Tenggara.',
            'is_published' => true,
        ]);

        $product = Product::create([
            'name' => 'Zine Sinema Edisi 05',
            'slug' => 'zine-sinema-edisi-05',
            'price' => 45000,
            'is_available' => true,
        ]);

        // 1. Visit Homepage
        $homeResponse = $this->get('/');
        $homeResponse->assertStatus(200);
        $homeResponse->assertSee('Evolusi Festival Film Indonesia Kontemporer');
        $homeResponse->assertSee('Pemutaran Layar Komunitas 2026');
        $homeResponse->assertSee('Wawancara');

        // 2. Visit Articles index & detail
        $articlesResponse = $this->get('/artikel');
        $articlesResponse->assertStatus(200);
        $articlesResponse->assertSee('Evolusi Festival Film Indonesia Kontemporer');

        $detailResponse = $this->get('/artikel/'.$article->slug);
        $detailResponse->assertStatus(200);
        $detailResponse->assertSee('Teks naskah lengkap artikel kurasi mendalam.');
        $detailResponse->assertSee('Sinema Alternatif');

        // 3. Visit Categories
        $categoriesResponse = $this->get('/kategori');
        $categoriesResponse->assertStatus(200);
        $categoriesResponse->assertSee('Wawancara');

        $catShowResponse = $this->get('/kategori/'.$category->slug);
        $catShowResponse->assertStatus(200);
        $catShowResponse->assertSee('Evolusi Festival Film Indonesia Kontemporer');

        // 4. Visit Tags
        $tagShowResponse = $this->get('/tag/'.$tag->slug);
        $tagShowResponse->assertStatus(200);
        $tagShowResponse->assertSee('Evolusi Festival Film Indonesia Kontemporer');

        // 5. Search
        $searchResponse = $this->get('/cari?q=Evolusi');
        $searchResponse->assertStatus(200);
        $searchResponse->assertSee('Evolusi Festival Film Indonesia Kontemporer');

        // 6. Events / Calendar
        $eventsResponse = $this->get('/events');
        $eventsResponse->assertStatus(200);
        $eventsResponse->assertSee('Pemutaran Layar Komunitas 2026');

        $eventDetail = $this->get('/events/'.$event->slug);
        $eventDetail->assertStatus(200);
        $eventDetail->assertSee('Kolektif Cinema');

        // 7. Directory: Experts, Shop, Authors
        $this->get('/rupa-jasa/tenaga-ahli')->assertStatus(200)->assertSee('Sari Filmologis');
        $this->get('/toko')->assertStatus(200)->assertSee('Zine Sinema Edisi 05');
        $this->get('/authors-list')->assertStatus(200)->assertSee('Redaktur Utama');
        $this->get('/rupa-jasa')->assertStatus(200)->assertSee('Rupa Jasa Infoscreening');

        // 8. Static Pages
        $this->get('/tentang-kami')->assertStatus(200)->assertSee('Tentang Kami');
        $this->get('/kontak')->assertStatus(200)->assertSee('kontak@infoscreening.id');
        $this->get('/kebijakan-privasi')->assertStatus(200)->assertSee('Kebijakan Privasi');
        $this->get('/halaman-fiktif-404')->assertNotFound();
    }

    public function test_complete_super_admin_strapi_cms_crud_lifecycle(): void
    {
        $admin = User::factory()->superAdmin()->create();

        // 1. Create Category via Strapi CMS
        $catResponse = $this->actingAs($admin)->post('/strapi/categories', [
            'name' => 'Resensi Spesial',
            'description' => 'Rubrik resensi eksklusif.',
            'color' => '#10B981',
        ]);
        $catResponse->assertRedirect('/strapi/categories');
        $category = Category::where('slug', 'resensi-spesial')->firstOrFail();

        // 2. Create Tag via Strapi CMS
        $tagResponse = $this->actingAs($admin)->post('/strapi/tags', [
            'name' => 'JAFF 2026',
            'description' => 'Jogja-NETPAC Asian Film Festival.',
        ]);
        $tagResponse->assertRedirect('/strapi/tags');
        $tag = Tag::where('slug', 'jaff-2026')->firstOrFail();

        // 3. Create Article via Strapi CMS
        $artResponse = $this->actingAs($admin)->post('/strapi/articles', [
            'title' => 'Resensi Film JAFF Terpilih',
            'content' => 'Ulasan mendalam penayangan film pembuka.',
            'status' => 'published',
            'categories' => [$category->id],
            'tags' => [$tag->id],
        ]);
        $artResponse->assertRedirect('/strapi/articles');
        $article = Article::where('slug', 'resensi-film-jaff-terpilih')->firstOrFail();
        $this->assertSame('published', $article->status);

        // 4. Update Article
        $updateResponse = $this->actingAs($admin)->put(route('strapi.articles.update', $article), [
            'title' => 'Resensi Film JAFF Terpilih (Revisi)',
            'content' => 'Ulasan mendalam penayangan film pembuka versi revisi.',
            'status' => 'published',
            'categories' => [$category->id],
            'tags' => [$tag->id],
        ]);
        $updateResponse->assertRedirect('/strapi/articles');
        $article = $article->fresh();
        $this->assertSame('Resensi Film JAFF Terpilih (Revisi)', $article->title);

        // 5. Create Event via Strapi CMS
        $eventResponse = $this->actingAs($admin)->post('/strapi/events', [
            'title' => 'Lokakarya Kritik Film',
            'type' => 'Workshop',
            'venue' => 'Rumah Sinema',
            'city' => 'Yogyakarta',
            'starts_at' => now()->addWeeks(2)->format('Y-m-d\TH:i'),
            'is_published' => true,
        ]);
        $eventResponse->assertRedirect('/strapi/events');
        $this->assertDatabaseHas('events', ['title' => 'Lokakarya Kritik Film']);

        // 6. Create Expert via Strapi CMS
        $expertResponse = $this->actingAs($admin)->post('/strapi/experts', [
            'name' => 'Bambang Sutradara',
            'specialty' => 'Sutradara Fiksi',
            'city' => 'Jakarta',
            'bio' => 'Sutradara film panjang independen.',
            'is_published' => true,
        ]);
        $expertResponse->assertRedirect('/strapi/experts');
        $this->assertDatabaseHas('experts', ['name' => 'Bambang Sutradara']);

        // 7. Create Product via Strapi CMS
        $productResponse = $this->actingAs($admin)->post('/strapi/products', [
            'name' => 'Kaos Sinema Infoscreening',
            'price' => 125000,
            'description' => 'Kaos katun combed 30s premium.',
            'is_available' => true,
        ]);
        $productResponse->assertRedirect('/strapi/products');
        $this->assertDatabaseHas('products', ['name' => 'Kaos Sinema Infoscreening']);

        // 8. Public Submits Contribution -> Admin Reviews and Approves
        $submitResponse = $this->post('/berkontribusi', [
            'name' => 'Dewi Film',
            'email' => 'dewi@cinema.id',
            'type' => 'Artikel',
            'title' => 'Liputan Pemutaran Bioskop Keliling',
            'message' => 'Naskah lengkap liputan bioskop keliling desa nelayan di pesisir.',
        ]);
        $submitResponse->assertRedirect('/berkontribusi');
        $contribution = Contribution::where('email', 'dewi@cinema.id')->firstOrFail();
        $this->assertSame('pending', $contribution->status);

        // Admin updates status to approved
        $reviewResponse = $this->actingAs($admin)->put('/strapi/contributions/'.$contribution->id, [
            'status' => 'approved',
        ]);
        $reviewResponse->assertRedirect('/strapi/contributions/'.$contribution->id);
        $this->assertSame('approved', $contribution->fresh()->status);

        // 9. Delete Article via Strapi CMS
        $delResponse = $this->actingAs($admin)->delete(route('strapi.articles.destroy', $article));
        $delResponse->assertRedirect('/strapi/articles');
        $this->assertDatabaseMissing('articles', ['id' => $article->id]);
    }
}
