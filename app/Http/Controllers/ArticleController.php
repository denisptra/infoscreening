<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::query()
            ->published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(10);

        return view('articles.index', compact('articles'));
    }

    public function show(Request $request, Article $article): View
    {
        abort_unless($article->status === 'published' && $article->published_at?->isPast(), 404);

        // Count one read per session to avoid write amplification from refreshes and crawlers.
        $viewedKey = 'article_viewed_'.$article->id;

        if (! $request->session()->has($viewedKey)) {
            $article->increment('views');
            $request->session()->put($viewedKey, true);
        }

        $article->load(['author', 'categories', 'tags']);

        $related = Article::query()
            ->published()
            ->whereKeyNot($article->id)
            ->whereHas('categories', fn ($query) => $query->whereIn('categories.id', $article->categories->pluck('id')))
            ->with(['categories'])
            ->latest('published_at')
            ->limit(3)
            ->get();

        if ($related->isEmpty()) {
            $related = Article::query()
                ->published()
                ->whereKeyNot($article->id)
                ->with(['categories'])
                ->latest('published_at')
                ->limit(3)
                ->get();
        }

        return view('articles.show', compact('article', 'related'));
    }
}
