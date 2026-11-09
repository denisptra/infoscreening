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
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
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
            $activeItems = fn (string $location) => NavigationItem::query()
                ->where('location', $location)
                ->whereNull('parent_id')
                ->where('is_active', true)
                ->where(function (Builder $query): void {
                    $query->where(function (Builder $query): void {
                        $query->whereNotNull('url')->where('url', '!=', '');
                    })->orWhereHas('children', fn (Builder $query) => $query
                        ->where('is_active', true)
                        ->whereNotNull('url')
                        ->where('url', '!=', ''));
                })
                ->with(['children' => fn ($query) => $query
                    ->where('is_active', true)
                    ->whereNotNull('url')
                    ->where('url', '!=', '')])
                ->orderBy('sort_order')
                ->get();

            $footerSettings = SiteSetting::query()
                ->whereIn('key', ['footer_about', 'footer_copyright'])
                ->pluck('value', 'key');

            $allFooterMenuItems = $activeItems('footer');
            $footerRubricMenu = $allFooterMenuItems->firstWhere('label', 'Rubrikasi Artikel');
            $footerCategories = Category::query()
                ->orderBy('name')
                ->limit(6)
                ->get(['id', 'name', 'slug']);

            if ($footerRubricMenu) {
                $footerCategoryMenuUrls = $footerRubricMenu->children->pluck('url');
                $footerCategories = $footerCategories
                    ->reject(fn (Category $category): bool => $footerCategoryMenuUrls->contains(
                        route('categories.show', ['category' => $category], false)
                    ))
                    ->values();
            }

            $view->with([
                'headerMenuItems' => $activeItems('header'),
                'footerRubricMenu' => $footerRubricMenu,
                'footerMenuItems' => $allFooterMenuItems
                    ->reject(fn (NavigationItem $item): bool => in_array($item->label, ['Rubrikasi Artikel', 'Ikuti Kami'], true))
                    ->values(),
                'footerCategories' => $footerCategories,
                'footerTags' => Tag::query()
                    ->withCount('articles')
                    ->orderByDesc('articles_count')
                    ->orderBy('name')
                    ->limit(9)
                    ->get(['id', 'name', 'slug']),
                'footerAbout' => $footerSettings->get(
                    'footer_about',
                    'Media informasi pemutaran sinema alternatif, festival film, dan ekosistem perfilman Indonesia.'
                ),
                'footerCopyright' => $footerSettings->get('footer_copyright', 'Infoscreening.id'),
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
