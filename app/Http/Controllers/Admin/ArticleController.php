<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ArticleController extends Controller
{
    use HandlesImageUploads;

    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Article::class);

        $search = trim((string) $request->get('q', ''));
        $status = $request->string('status')->toString();

        $articles = Article::query()
            ->with(['categories', 'author'])
            ->when(
                $request->user()->isEditor(),
                fn ($query) => $query->whereBelongsTo($request->user(), 'author')
            )
            ->when($search !== '', fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->when(in_array($status, ['draft', 'published'], true), fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('admin.articles.index', compact('articles', 'search', 'status'));
    }

    public function create(): View
    {
        Gate::authorize('create', Article::class);

        return view('admin.articles.form', [
            'article' => new Article(['status' => 'draft']),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('create', Article::class);

        $data = $this->validated($request);
        $data['user_id'] = $request->user()->id;
        if ($request->user()->isEditor()) {
            $data['status'] = 'draft';
            $data['published_at'] = null;
            $data['is_featured'] = false;
        }
        $data['featured_image'] = $this->resolveImage($request, 'featured_image', null, 'articles');

        $article = Article::create($data);
        $this->syncRelations($article, $request);

        return redirect()->route('strapi.articles.index')->with('status', 'Artikel berhasil dibuat.');
    }

    public function edit(Article $article): View
    {
        Gate::authorize('update', $article);

        return view('admin.articles.form', [
            'article' => $article->load(['categories', 'tags']),
            'categories' => Category::orderBy('name')->get(),
            'tags' => Tag::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, Article $article): RedirectResponse
    {
        Gate::authorize('update', $article);

        $data = $this->validated($request, $article);
        if ($request->user()->isEditor()) {
            $data['status'] = 'draft';
            $data['published_at'] = null;
            $data['is_featured'] = false;
        }
        $data['featured_image'] = $this->resolveImage($request, 'featured_image', $article->featured_image, 'articles');

        $article->update($data);
        $this->syncRelations($article, $request);

        return redirect()->route('strapi.articles.index')->with('status', 'Artikel berhasil diperbarui.');
    }

    public function destroy(Article $article): RedirectResponse
    {
        Gate::authorize('delete', $article);

        $this->deleteStoredImage($article->featured_image);
        $article->delete();

        return redirect()->route('strapi.articles.index')->with('status', 'Artikel dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Article $article = null): array
    {
        // Konten HTML dikirim dalam base64 dari form untuk menghindari blokir WAF cPanel.
        if ($request->boolean('content_is_encoded') && filled($request->input('content_encoded'))) {
            $decoded = base64_decode((string) $request->input('content_encoded'), true);

            if ($decoded === false) {
                throw ValidationException::withMessages([
                    'content' => 'Konten artikel tidak valid. Silakan muat ulang halaman lalu coba lagi.',
                ]);
            }

            $request->merge(['content' => $decoded]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'slug' => ['nullable', 'string', 'max:220', 'alpha_dash', Rule::unique('articles', 'slug')->ignore($article?->id)],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'status' => ['required', Rule::in(['draft', 'published'])],
            'published_at' => ['nullable', 'date'],
            'is_featured' => ['nullable', 'boolean'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
            'featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'direct_featured_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'featured_image_url' => ['nullable', 'url', 'max:500'],
            'categories' => ['nullable', 'array'],
            'categories.*' => ['exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $data = collect($validated)->only([
            'title', 'slug', 'excerpt', 'content', 'status', 'meta_title', 'meta_description',
        ])->all();

        $data['slug'] = filled($validated['slug'] ?? null) ? $validated['slug'] : Str::slug($validated['title']);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['reading_time'] = max(1, (int) ceil(str_word_count(strip_tags($validated['content'])) / 200));
        $data['published_at'] = $validated['status'] === 'published'
            ? ($validated['published_at'] ?? $article?->published_at ?? now())
            : ($validated['published_at'] ?? null);

        return $data;
    }

    private function syncRelations(Article $article, Request $request): void
    {
        $article->categories()->sync($request->input('categories', []));
        $article->tags()->sync($request->input('tags', []));

        if ($article->is_featured) {
            Article::where('id', '!=', $article->id)->where('is_featured', true)->update(['is_featured' => false]);
        }
    }
}
