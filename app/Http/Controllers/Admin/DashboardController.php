<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Contribution;
use App\Models\Event;
use App\Models\Expert;
use App\Models\Media;
use App\Models\Product;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
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

        $categoryBreakdown = Category::query()
            ->withCount('articles')
            ->orderByDesc('articles_count')
            ->limit(6)
            ->get();

        return view('admin.dashboard', [
            'stats' => $stats,
            'months' => $months,
            'categoryBreakdown' => $categoryBreakdown,
            'topArticles' => Article::query()->orderByDesc('views')->limit(5)->get(),
            'recentArticles' => Article::query()->with('categories')->latest()->limit(6)->get(),
            'pendingContributions' => Contribution::where('status', 'pending')->latest()->limit(5)->get(),
            'upcomingEvents' => Event::upcoming()->orderBy('starts_at')->limit(4)->get(),
        ]);
    }
}
