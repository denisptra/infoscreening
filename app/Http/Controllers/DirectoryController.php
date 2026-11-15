<?php

namespace App\Http\Controllers;

use App\Models\Expert;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class DirectoryController extends Controller
{
    public function authors(): View
    {
        $authors = User::query()
            ->whereHas('articles', fn ($query) => $query->published())
            ->withCount(['articles' => fn ($query) => $query->published()])
            ->orderByDesc('articles_count')
            ->get();

        return view('directory.authors', compact('authors'));
    }

    public function services(): View
    {
        return view('directory.services');
    }

    public function experts(): View
    {
        $experts = Expert::query()->where('is_published', true)->orderBy('name')->get();

        return view('directory.experts', compact('experts'));
    }

    public function shop(): View
    {
        $products = Product::query()->orderByDesc('is_available')->latest()->get();

        return view('directory.shop', compact('products'));
    }
}
