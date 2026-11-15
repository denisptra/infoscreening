<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::query()
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->orderBy('name')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        $articles = $category->articles()
            ->published()
            ->with(['author', 'categories'])
            ->latest('published_at')
            ->paginate(10);

        return view('categories.show', compact('category', 'articles'));
    }
}
