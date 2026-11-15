@extends('layouts.admin')

@section('page_title', 'Content Manager: Products & Zines')

@section('content')
<div class="space-y-6">

    <!-- Strapi Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Store & Merch</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $products->count() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Physical print zines, official merchandise, and cinema prints.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('strapi.products.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create new entry</span>
            </a>
        </div>
    </div>

    <!-- Strapi Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse ($products as $prod)
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden flex flex-col justify-between hover:border-[#4945ff]/40 transition">
                <div>
                    <div class="aspect-video w-full bg-[#f6f6f9] border-b border-[#eaeaef] overflow-hidden">
                        <img loading="lazy" decoding="async"
                            src="{{ $prod->image_url ?? 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=400&q=80' }}"
                            alt="{{ $prod->name }}"
                            class="w-full h-full object-cover"
                        >
                    </div>
                    <div class="p-4 space-y-1">
                        <h3 class="font-medium text-xs text-[#32324d] truncate">{{ $prod->name }}</h3>
                        <p class="text-sm font-semibold text-[#4945ff]">{{ $prod->formatted_price }}</p>
                        <p class="text-[11px] text-[#8e8ea9] line-clamp-2 leading-relaxed">{{ $prod->description }}</p>
                    </div>
                </div>

                <div class="p-4 pt-0">
                    <div class="pt-3 border-t border-[#eaeaef] flex items-center justify-between text-xs">
                        @if ($prod->is_available)
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> In Stock
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[11px] font-normal text-rose-500">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Sold Out
                            </span>
                        @endif

                        <div class="flex items-center gap-2 whitespace-nowrap">
                            <a href="{{ route('strapi.products.edit', $prod) }}" class="p-1 text-[#666687] hover:text-[#4945ff]" title="Edit Entry">
                                <i class="far fa-edit"></i>
                            </a>
                            <form method="POST" action="{{ route('strapi.products.destroy', $prod) }}" class="inline" data-confirm data-confirm-title="Hapus produk?" data-confirm-message="Produk ini akan dihapus permanen." data-confirm-action="Hapus produk">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1 text-[#8e8ea9] hover:text-red-600" title="Delete">
                                    <i class="far fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-8 rounded-xl border border-[#eaeaef] text-center text-[#8e8ea9] text-xs">
                No products found.
            </div>
        @endforelse
    </div>
</div>
@endsection
