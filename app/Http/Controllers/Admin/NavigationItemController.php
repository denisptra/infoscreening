<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class NavigationItemController extends Controller
{
    public function index(): View
    {
        $items = NavigationItem::query()
            ->whereNull('parent_id')
            ->with('children')
            ->orderBy('location')
            ->orderBy('sort_order')
            ->get()
            ->groupBy('location');

        return view('admin.navigation.index', [
            'itemsByLocation' => $items,
            'footerAbout' => SiteSetting::query()->where('key', 'footer_about')->value('value'),
            'footerCopyright' => SiteSetting::query()->where('key', 'footer_copyright')->value('value'),
        ]);
    }

    public function create(): View
    {
        return view('admin.navigation.form', [
            'navigation' => new NavigationItem(['location' => 'header', 'is_active' => true]),
            'parents' => NavigationItem::query()->whereNull('parent_id')->orderBy('location')->orderBy('sort_order')->get(),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->validateParent($data);
        $data['is_active'] = $request->boolean('is_active');
        $data['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        NavigationItem::create($data);

        return redirect()->route('strapi.navigation.index')->with('status', 'Menu berhasil ditambahkan.');
    }

    public function edit(NavigationItem $navigation): View
    {
        return view('admin.navigation.form', [
            'navigation' => $navigation,
            'parents' => NavigationItem::query()
                ->whereNull('parent_id')
                ->where('id', '!=', $navigation->id)
                ->orderBy('location')
                ->orderBy('sort_order')
                ->get(),
            'categories' => Category::query()->orderBy('name')->get(['id', 'name', 'slug']),
        ]);
    }

    public function normalizeUrls(): RedirectResponse
    {
        $count = 0;
        $items = NavigationItem::query()->whereNotNull('url')->where('url', '!=', '')->get();

        foreach ($items as $item) {
            $original = $item->url;
            $normalized = $original;

            if (str_contains($normalized, '/category/')) {
                $normalized = str_replace('/category/', '/kategori/', $normalized);
            }
            if ($normalized === '/category') {
                $normalized = '/kategori';
            }

            if ($normalized !== $original) {
                $item->update(['url' => $normalized]);
                $count++;
            }
        }

        return redirect()->route('strapi.navigation.index')
            ->with('status', "Berhasil menormalkan {$count} tautan menu ke standar /kategori/.");
    }

    public function update(Request $request, NavigationItem $navigation): RedirectResponse
    {
        $data = $this->validated($request);
        $this->validateParent($data, $navigation);
        $data['is_active'] = $request->boolean('is_active');
        $data['open_in_new_tab'] = $request->boolean('open_in_new_tab');

        $navigation->update($data);

        return redirect()->route('strapi.navigation.index')->with('status', 'Menu berhasil diperbarui.');
    }

    public function destroy(NavigationItem $navigation): RedirectResponse
    {
        $navigation->delete();

        return redirect()->route('strapi.navigation.index')->with('status', 'Menu dan submenu terkait berhasil dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => ['required', 'string', 'max:100'],
            'location' => ['required', 'in:header,footer'],
            'url' => [
                'nullable',
                'string',
                'max:2048',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if ($value !== null && $value !== ''
                        && ! (str_starts_with($value, '/') && ! str_starts_with($value, '//'))
                        && ! str_starts_with($value, 'http://')
                        && ! str_starts_with($value, 'https://')) {
                        $fail('Gunakan alamat lokal seperti /artikel atau tautan http/https.');
                    }
                },
            ],
            'parent_id' => ['nullable', 'integer', 'exists:navigation_items,id'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:1000'],
            'open_in_new_tab' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        if (! empty($data['url'])) {
            $data['url'] = trim($data['url']);
            if (str_starts_with($data['url'], '/category/')) {
                $data['url'] = '/kategori/' . substr($data['url'], 10);
            } elseif ($data['url'] === '/category') {
                $data['url'] = '/kategori';
            }
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function validateParent(array $data, ?NavigationItem $navigation = null): void
    {
        if (blank($data['parent_id'] ?? null)) {
            return;
        }

        $parent = NavigationItem::query()->findOrFail($data['parent_id']);
        $hasChildren = $navigation?->children()->exists() ?? false;

        if (
            $parent->parent_id !== null
            || $parent->location !== $data['location']
            || $parent->id === $navigation?->id
            || $hasChildren
        ) {
            throw ValidationException::withMessages([
                'parent_id' => 'Pilih menu utama dari area yang sama. Submenu hanya boleh satu tingkat.',
            ]);
        }
    }
}
