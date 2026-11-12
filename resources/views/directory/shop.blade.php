@extends('layouts.site')

@section('title', 'Toko & Merchandise - Infoscreening')
@section('description', 'Koleksi zine cetak eksklusif, kaos sinema, tote bag, dan merchandise resmi Infoscreening.')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
                <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Home</a>
                <i class="fas fa-angle-right text-[10px]"></i>
                <span class="text-[#3A07BB] font-medium">Toko</span>
            </nav>
            <span class="badge-cat mb-2">Merchandise Resmi</span>
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                Toko &amp; Zine Infoscreening
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed font-normal">
                Dukung operasional independen Infoscreening dengan mengoleksi zine terbitan khusus dan cinderamata karya kami.
            </p>
        </div>

        <div class="text-xs text-slate-600 bg-purple-50 px-4 py-2.5 rounded-xl border border-purple-200 font-normal flex items-center gap-2">
            <i class="fas fa-truck text-[#3A07BB]"></i>
            <span>Pengiriman ke seluruh Indonesia</span>
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse ($products as $product)
            <div class="bg-white border border-slate-200/80 rounded-2xl overflow-hidden shadow-card hover:border-[#3A07BB]/30 hover:shadow-card-hover transition duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative overflow-hidden aspect-[4/3] bg-slate-100">
                        <img loading="lazy" decoding="async"
                            src="{{ $product->image_url ?? 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?auto=format&fit=crop&w=600&q=80' }}"
                            alt="{{ $product->name }}"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        >
                        @if (! $product->is_available)
                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center">
                                <span class="bg-rose-600 text-white text-[11px] font-medium px-3 py-1 rounded-full">Habis</span>
                            </div>
                        @endif
                    </div>

                    <div class="p-4 space-y-1.5">
                        <h3 class="font-sans font-semibold text-sm text-slate-900 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                            {{ $product->name }}
                        </h3>
                        <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-normal">
                            {{ $product->description }}
                        </p>
                    </div>
                </div>

                <div class="p-4 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                        <span class="font-sans font-semibold text-sm text-[#3A07BB]">
                            {{ $product->formatted_price }}
                        </span>
                        @if ($product->is_available)
                            <a
                                href="{{ $product->buy_url ?? '#' }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="px-3.5 py-1.5 bg-[#3A07BB] hover:bg-[#2e0594] text-white font-medium text-xs rounded-lg transition shadow-xs"
                            >
                                Beli &rarr;
                            </a>
                        @else
                            <span class="text-xs text-slate-400 font-normal">Stok Kosong</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200/80">
                <p class="text-xs text-slate-500 font-normal">Belum ada produk di toko saat ini.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
