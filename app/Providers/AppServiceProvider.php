<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\Article;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use App\Models\Tag;
use App\Policies\ArticlePolicy;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::policy(Article::class, ArticlePolicy::class);

        ResetPassword::toMailUsing(function (object $notifiable, string $token): MailMessage {
            $url = url(route('password.reset', [
                'token' => $token,
                'email' => $notifiable->getEmailForPasswordReset(),
            ], false));

            $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

            return (new MailMessage)
                ->subject('Atur Ulang Password — Infoscreening')
                ->greeting('Halo, '.($notifiable->name ?: 'Pengguna Infoscreening').'!')
                ->line('Anda menerima email ini karena kami menerima permintaan pengaturan ulang password untuk akun Anda.')
                ->action('Atur Ulang Password', $url)
                ->line("Tautan ini hanya berlaku selama {$expireMinutes} menit.")
                ->line('Jika Anda tidak merasa meminta pengaturan ulang password, abaikan saja email ini.');
        });

        if (! file_exists(public_path('storage')) && file_exists(storage_path('app/public')) && function_exists('symlink')) {
            @symlink(storage_path('app/public'), public_path('storage'));
        }

        ViewFacade::composer('layouts.admin', function (View $view): void {
            $user = auth()->user();
            $notifications = collect();
            $notificationCount = 0;
            $notificationUrl = route('strapi.articles.index');
            $emptyNotificationMessage = 'Belum ada tugas baru untuk Anda.';

            if ($user->isSuperAdmin()) {
                $pendingContributions = Contribution::query()->where('status', 'pending');
                $editorDrafts = Article::query()
                    ->where('status', 'draft')
                    ->whereHas('author', fn (Builder $query): Builder => $query->where('role', UserRole::Editor->value));

                $notificationCount = (clone $pendingContributions)->count() + (clone $editorDrafts)->count();
                $notifications = $pendingContributions->latest()
                    ->limit(5)
                    ->get(['id', 'title', 'name', 'created_at'])
                    ->map(fn (Contribution $contribution): array => [
                        'title' => 'Kontribusi menunggu tinjauan',
                        'description' => $contribution->title.' dari '.$contribution->name,
                        'url' => route('strapi.contributions.show', ['contribution' => $contribution]),
                        'createdAt' => $contribution->created_at,
                    ]);
                $notifications = $notifications->concat(
                    $editorDrafts->with('author:id,name')
                        ->latest()
                        ->limit(5)
                        ->get(['id', 'title', 'slug', 'user_id', 'created_at'])
                        ->map(fn (Article $article): array => [
                            'title' => 'Draf editor perlu ditinjau',
                            'description' => $article->title.' dari '.$article->author?->name,
                            'url' => route('strapi.articles.edit', ['article' => $article]),
                            'createdAt' => $article->created_at,
                        ])
                );
                $notificationUrl = route('strapi.contributions.index');
                $emptyNotificationMessage = 'Inbox dan draf editor sudah ditinjau.';
            } elseif ($user->isEditorialAdmin()) {
                $editorDrafts = Article::query()
                    ->where('status', 'draft')
                    ->whereHas('author', fn (Builder $query): Builder => $query->where('role', UserRole::Editor->value));

                $notificationCount = (clone $editorDrafts)->count();
                $notifications = $editorDrafts->with('author:id,name')
                    ->latest()
                    ->limit(5)
                    ->get(['id', 'title', 'slug', 'user_id', 'created_at'])
                    ->map(fn (Article $article): array => [
                        'title' => 'Draf editor perlu ditinjau',
                        'description' => $article->title.' dari '.$article->author?->name,
                        'url' => route('strapi.articles.edit', ['article' => $article]),
                        'createdAt' => $article->created_at,
                    ]);
                $emptyNotificationMessage = 'Belum ada draf editor yang menunggu tinjauan.';
            }

            $view->with([
                'notifications' => $notifications->sortByDesc('createdAt')->take(5)->values(),
                'notificationCount' => $notificationCount,
                'notificationUrl' => $notificationUrl,
                'emptyNotificationMessage' => $emptyNotificationMessage,
            ]);
        });

        ViewFacade::composer('layouts.site', function (View $view): void {
            $topCategories = Category::query()
                ->withCount(['articles' => fn ($query) => $query->published()])
                ->orderByDesc('articles_count')
                ->orderBy('name')
                ->limit(6)
                ->get(['id', 'name', 'slug', 'color']);

            $allTags = Tag::query()
                ->withCount(['articles' => fn ($query) => $query->published()])
                ->orderByDesc('articles_count')
                ->orderBy('name')
                ->limit(8)
                ->get(['id', 'name', 'slug']);

            // Navbar konsisten berbahasa Indonesia dengan dropdown ringkas
            $home = tap(new NavigationItem(['location' => 'header', 'label' => 'Beranda', 'url' => '/', 'sort_order' => 10, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect()));

            $articleChildren = collect([
                tap(new NavigationItem([
                    'location' => 'header',
                    'label' => 'Semua Artikel',
                    'url' => '/artikel',
                    'sort_order' => 5,
                    'open_in_new_tab' => false,
                    'is_active' => true,
                ]), fn ($i) => $i->setRelation('children', collect())),
            ])->merge(
                $topCategories->values()->map(function (Category $category, int $index): NavigationItem {
                    $child = new NavigationItem([
                        'location' => 'header',
                        'label' => $category->name,
                        'url' => '/kategori/'.$category->slug,
                        'sort_order' => ($index + 1) * 10,
                        'open_in_new_tab' => false,
                        'is_active' => true,
                    ]);
                    $child->setRelation('children', collect());

                    return $child;
                })
            )->push(
                tap(new NavigationItem([
                    'location' => 'header',
                    'label' => 'Semua Kategori',
                    'url' => '/kategori',
                    'sort_order' => 999,
                    'open_in_new_tab' => false,
                    'is_active' => true,
                ]), fn ($i) => $i->setRelation('children', collect()))
            );
            $artikel = tap(new NavigationItem(['location' => 'header', 'label' => 'Artikel', 'url' => '/artikel', 'sort_order' => 20, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', $articleChildren));

            $rupaJasaChildren = collect([
                tap(new NavigationItem(['location' => 'header', 'label' => 'Semua Rupa Jasa', 'url' => '/rupa-jasa', 'sort_order' => 5, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'header', 'label' => 'Direktori Penulis', 'url' => '/authors-list', 'sort_order' => 10, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'header', 'label' => 'Tenaga Ahli', 'url' => '/rupa-jasa/tenaga-ahli', 'sort_order' => 20, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'header', 'label' => 'Toko', 'url' => '/toko', 'sort_order' => 30, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
            ]);
            $rupaJasa = tap(new NavigationItem(['location' => 'header', 'label' => 'Rupa Jasa', 'url' => '/rupa-jasa', 'sort_order' => 40, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', $rupaJasaChildren));

            $headerMenuItems = collect([
                $home,
                $artikel,
                tap(new NavigationItem(['location' => 'header', 'label' => 'Kalender', 'url' => '/events', 'sort_order' => 30, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                $rupaJasa,
                tap(new NavigationItem(['location' => 'header', 'label' => 'Tentang', 'url' => '/tentang-kami', 'sort_order' => 50, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'header', 'label' => 'Kontak', 'url' => '/kontak', 'sort_order' => 60, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
            ]);

            $ecosystemChildren = collect([
                tap(new NavigationItem(['location' => 'footer', 'label' => 'Kalender Acara Sinema', 'url' => '/events', 'sort_order' => 10, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'footer', 'label' => 'Kirim Liputan / Naskah', 'url' => '/berkontribusi', 'sort_order' => 20, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'footer', 'label' => 'Tenaga Ahli Film', 'url' => '/rupa-jasa/tenaga-ahli', 'sort_order' => 30, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'footer', 'label' => 'Daftar Penulis', 'url' => '/authors-list', 'sort_order' => 40, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'footer', 'label' => 'Toko Zine & Merchandise', 'url' => '/toko', 'sort_order' => 50, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
                tap(new NavigationItem(['location' => 'footer', 'label' => 'Kontak Redaksi', 'url' => '/kontak', 'sort_order' => 60, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', collect())),
            ]);
            $ecosystemGroup = tap(new NavigationItem(['location' => 'footer', 'label' => 'Ekosistem Sinema', 'url' => null, 'sort_order' => 10, 'open_in_new_tab' => false, 'is_active' => true]), fn ($i) => $i->setRelation('children', $ecosystemChildren));

            $view->with([
                'headerMenuItems' => $headerMenuItems,
                'footerRubricMenu' => null,
                'footerMenuItems' => collect([$ecosystemGroup]),
                'footerCategories' => $topCategories,
                'footerTags' => $allTags,
                'footerAbout' => 'Media informasi pemutaran sinema alternatif, festival film, dan ekosistem perfilman Indonesia.',
                'footerCopyright' => 'Infoscreening.id',
            ]);
        });

        $host = request()->getHost();
        $isExternalDomain = ! in_array($host, ['localhost', '127.0.0.1']) && ! str_starts_with($host, '192.168.');

        if (
            request()->header('x-forwarded-proto') === 'https'
            || request()->server('HTTP_X_FORWARDED_PROTO') === 'https'
            || request()->isSecure()
            || $isExternalDomain
        ) {
            URL::forceScheme('https');
        }

        // Prevent broken layout/styles caused by stale Vite "hot" reload file when dev server is closed
        if (file_exists(public_path('hot'))) {
            $hotUrl = trim((string) @file_get_contents(public_path('hot')));
            $parts = parse_url($hotUrl);
            $host = $parts['host'] ?? '127.0.0.1';
            if ($host === '[::1]' || $host === '::1') {
                $host = '127.0.0.1';
            }
            $port = (int) ($parts['port'] ?? 5173);

            $socket = @fsockopen($host, $port, $errno, $errstr, 0.05);
            if (! is_resource($socket)) {
                @unlink(public_path('hot'));
            } else {
                fclose($socket);
            }
        }
    }
}
