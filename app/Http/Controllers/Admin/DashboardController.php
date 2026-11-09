<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
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
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();

        if (! $user->isSuperAdmin()) {
            $articles = Article::query()
                ->when(
                    $user->isEditor(),
                    fn ($query) => $query->whereBelongsTo($user, 'author')
                );

            return view('admin.editor-dashboard', [
                'role' => $user->role,
                'stats' => [
                    'articles' => (clone $articles)->count(),
                    'published' => (clone $articles)->where('status', 'published')->count(),
                    'drafts' => (clone $articles)->where('status', 'draft')->count(),
                ],
                'recentArticles' => (clone $articles)
                    ->with('categories')
                    ->latest()
                    ->limit(8)
                    ->get(),
            ]);
        }

        $stats = [
            'articles' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'drafts' => Article::where('status', 'draft')->count(),
            'views' => (int) Article::sum('views'),
            'events' => Event::upcoming()->count(),
            'pending' => Contribution::where('status', 'pending')->count(),
            'experts' => Expert::count(),
            'products' => Product::count(),
            'media' => Media::count(),
            'categories' => Category::count(),
            'tags' => Tag::count(),
            'users' => User::count(),
            'admins' => User::where('role', UserRole::Admin->value)->count(),
            'editors' => User::where('role', UserRole::Editor->value)->count(),
        ];

        $months = collect(range(5, 0))->map(function (int $ago) {
            $month = now()->subMonths($ago);

            return [
                'label' => $month->translatedFormat('M'),
                'count' => Article::query()
                    ->whereYear('published_at', $month->year)
                    ->whereMonth('published_at', $month->month)
                    ->count(),
            ];
        });

        $articleMenu = NavigationItem::query()
            ->where('location', 'header')
            ->whereNull('parent_id')
            ->where('url', '/artikel')
            ->with(['children' => fn ($query) => $query
                ->where('is_active', true)
                ->whereNotNull('url')
                ->where('url', 'like', '/kategori/%')
                ->orderBy('sort_order')])
            ->first();
        $articleMenuItems = $articleMenu?->children ?? collect();
        $categorySlugs = $articleMenuItems
            ->map(fn (NavigationItem $item): string => Str::after($item->url, '/kategori/'))
            ->unique()
            ->values();
        $categoriesBySlug = Category::query()
            ->withCount('articles')
            ->whereIn('slug', $categorySlugs)
            ->get()
            ->keyBy('slug');
        $dashboardCategories = $articleMenuItems
            ->map(function (NavigationItem $item) use ($categoriesBySlug): ?Category {
                $category = $categoriesBySlug->get(Str::after($item->url, '/kategori/'));

                if (! $category) {
                    return null;
                }

                $category->setAttribute('menu_label', $item->label);

                return $category;
            })
            ->filter()
            ->values();
        $dashboardTags = Tag::query()
            ->withCount('articles')
            ->orderByDesc('articles_count')
            ->orderBy('name')
            ->limit(9)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'months' => $months,
            'dashboardCategories' => $dashboardCategories,
            'dashboardTags' => $dashboardTags,
            'topArticles' => Article::query()->orderByDesc('views')->limit(5)->get(),
            'recentArticles' => Article::query()->with('categories')->latest()->limit(6)->get(),
            'pendingContributions' => Contribution::where('status', 'pending')->latest()->limit(5)->get(),
            'upcomingEvents' => Event::upcoming()->orderBy('starts_at')->limit(4)->get(),
        ]);
    }
}
