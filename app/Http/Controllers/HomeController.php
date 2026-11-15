<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Event;
use App\Models\Tag;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $featured = Article::query()
            ->published()
            ->where('is_featured', true)
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at')
            ->first();

        if (! $featured) {
            $featured = Article::query()
                ->published()
                ->with(['author', 'categories', 'tags'])
                ->latest('published_at')
                ->first();
        }

        $spotlight = Article::query()
            ->published()
            ->when($featured, fn ($query) => $query->whereKeyNot($featured->id))
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        $excludedIds = collect([$featured?->id])
            ->merge($spotlight->pluck('id'))
            ->filter()
            ->all();

        $articles = Article::query()
            ->published()
            ->whereNotIn('id', $excludedIds)
            ->with(['author', 'categories', 'tags'])
            ->latest('published_at')
            ->paginate(8);

        $popularArticles = Article::query()
            ->published()
            ->with('categories')
            ->orderByDesc('views')
            ->limit(6)
            ->get();

        $popularTags = Tag::query()
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->orderByDesc('articles_count')
            ->limit(24)
            ->get();

        $upcomingEvents = Event::query()
            ->published()
            ->upcoming()
            ->orderBy('starts_at')
            ->limit(4)
            ->get();

        $categories = Category::query()
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return view('home', compact(
            'featured',
            'spotlight',
            'articles',
            'popularArticles',
            'popularTags',
            'upcomingEvents',
            'categories'
        ));
    }
}
