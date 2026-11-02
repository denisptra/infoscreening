@extends('layouts.admin')

@section('page_title', $product->exists ? 'Edit Product: ' . $product->name : 'Create an Entry: Product')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('strapi.products.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5 mb-1">
                <i class="fas fa-arrow-left text-[10px]"></i>
                <span>Back to Products list</span>
            </a>
            <h2 class="font-sans font-semibold text-xl text-[#32324d]">
                {{ $product->exists ? 'Edit product' : 'Create an entry' }}
            </h2>
        </div>
    </div>

    <form
        method="POST"
        action="{{ $product->exists ? route('strapi.products.update', $product) : route('strapi.products.store') }}"
        enctype="multipart/form-data"
        class="bg-white p-6 rounded-xl border border-[#eaeaef] shadow-strapi space-y-5"
    >
        @csrf
        @if ($product->exists)
            @method('PUT')
        @endif

        <div>
            <label for="name" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Product Title / Zine Edition <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="name"
                id="name"
                value="{{ old('name', $product->name) }}"
                required
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                placeholder="e.g. Zine Infoscreening Vol. 4"
            >
            @error('name')
                <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="price" class="block text-xs font-medium text-[#32324d] mb-1.5">
                    Price (IDR) <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    name="price"
                    id="price"
                    value="{{ old('price', $product->price) }}"
                    required
                    min="0"
                    step="1000"
                    class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                    placeholder="45000"
                >
            </div>
            <div>
                <label for="buy_url" class="block text-xs font-medium text-[#32324d] mb-1.5">
                    Store Checkout / Tokopedia / Shopee URL
                </label>
                <input
                    type="url"
                    name="buy_url"
                    id="buy_url"
                    value="{{ old('buy_url', $product->buy_url) }}"
                    class="w-full border border-[#dcdce4] rounded-md px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
                    placeholder="https://tokopedia.com/..."
                >
            </div>
        </div>

        <div>
            <x-strapi-media-picker
                name="image"
                label="Product / Merch Image Asset"
                :value="old('image', $product->image)"
                :imageUrl="$product->image_url"
            />
        </div>

        <div>
            <label for="description" class="block text-xs font-medium text-[#32324d] mb-1.5">
                Product Details & Specifications
            </label>
            <textarea
                name="description"
                id="description"
                rows="4"
                class="w-full border border-[#dcdce4] rounded-md px-3.5 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]"
            >{{ old('description', $product->description) }}</textarea>
        </div>

        <div>
            <label class="flex items-center gap-2 cursor-pointer">
                <input
                    type="checkbox"
                    name="is_available"
                    value="1"
                    {{ old('is_available', $product->is_available) ? 'checked' : '' }}
                    class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]"
                >
                <span class="text-xs font-medium text-[#32324d]">Item Available / In Stock</span>
            </label>
        </div>

        <div class="flex items-center justify-end gap-2 pt-4 border-t border-[#eaeaef]">
            <a href="{{ route('strapi.products.index') }}" class="px-3.5 py-2 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#666687] text-xs font-medium rounded-lg transition">
                Cancel
            </a>
            <button type="submit" class="px-4 py-2 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-lg transition shadow-sm">
                Save
            </button>
        </div>
    </form>
</div>
@endsection
