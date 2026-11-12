<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\Concerns\HandlesImageUploads;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use HandlesImageUploads;

    public function index(): View
    {
        return view('admin.products.index', ['products' => Product::latest()->get()]);
    }

    public function create(): View
    {
        return view('admin.products.form', ['product' => new Product(['is_available' => true])]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImage($request, 'image', null, 'products');

        Product::create($data);

        return redirect()->route('strapi.products.index')->with('status', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        return view('admin.products.form', compact('product'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request);
        $data['image'] = $this->resolveImage($request, 'image', $product->image, 'products');

        $product->update($data);

        return redirect()->route('strapi.products.index')->with('status', 'Produk berhasil diperbarui.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->deleteStoredImage($product->image);
        $product->delete();

        return redirect()->route('strapi.products.index')->with('status', 'Produk dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1500'],
            'price' => ['required', 'integer', 'min:0'],
            'buy_url' => ['nullable', 'url', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'direct_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'image_url' => ['nullable', 'url', 'max:500'],
        ]);

        unset($data['image'], $data['direct_image'], $data['image_url']);
        $data['is_available'] = $request->boolean('is_available');

        return $data;
    }
}
