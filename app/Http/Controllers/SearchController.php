<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $query = trim((string) ($validated['q'] ?? ''));
        $escaped = addcslashes($query, '%_\\');

        $articles = Article::query()
            ->published()
            ->when($query !== '', function ($builder) use ($escaped) {
                $builder->where(function ($inner) use ($escaped) {
                    $inner->where('title', 'like', "%{$escaped}%")
                        ->orWhere('excerpt', 'like', "%{$escaped}%")
                        ->orWhere('content', 'like', "%{$escaped}%");
                });
            })
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(10)
            ->appends(['q' => $query]);

        return view('search.index', compact('articles', 'query'));
    }
}
