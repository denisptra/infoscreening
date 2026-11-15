<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::query()->withCount('articles')->orderBy('name')->get();

        return view('admin.tags.index', compact('tags'));
    }

    public function create(): View
    {
        return view('admin.tags.form', ['tag' => new Tag]);
    }

    public function store(Request $request): RedirectResponse
    {
        Tag::create($this->validated($request));

        return redirect()->route('strapi.tags.index')->with('status', 'Tag berhasil dibuat.');
    }

    public function edit(Tag $tag): View
    {
        return view('admin.tags.form', compact('tag'));
    }

    public function update(Request $request, Tag $tag): RedirectResponse
    {
        $tag->update($this->validated($request, $tag));

        return redirect()->route('strapi.tags.index')->with('status', 'Tag berhasil diperbarui.');
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $tag->delete();

        return redirect()->route('strapi.tags.index')->with('status', 'Tag dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Tag $tag = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['nullable', 'string', 'max:120', 'alpha_dash', Rule::unique('tags', 'slug')->ignore($tag?->id)],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        $data['slug'] = filled($data['slug'] ?? null) ? $data['slug'] : Str::slug($data['name']);

        return $data;
    }
}
