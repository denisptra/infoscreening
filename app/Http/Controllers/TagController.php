<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Contracts\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return view('tags.index', compact('tags'));
    }

    public function show(Tag $tag): View
    {
        $articles = $tag->articles()
            ->published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(10);

        return view('tags.show', compact('tag', 'articles'));
    }
}
