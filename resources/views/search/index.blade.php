@extends('layouts.site')

@section('title', 'Pencarian: ' . ($query ?: 'Semua') . ' - Infoscreening')
@section('description', 'Cari artikel, ulasan, festival film, dan liputan sinema alternatif di Infoscreening.')

@section('content')
<div class="space-y-8">
    <!-- Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm">
        <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
            <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Beranda</a>
            <i class="fas fa-angle-right text-[9px]"></i>
            <span class="text-[#3A07BB] font-medium">Pencarian</span>
        </nav>

        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-800 tracking-tight">
            Hasil Pencarian
        </h1>
        <p class="text-xs text-slate-500 mt-1 max-w-xl font-normal leading-relaxed">
            @if ($query)
                Menampilkan hasil untuk kata kunci <strong class="text-[#3A07BB]">"{{ $query }}"</strong>.
            @else
                Ketikkan kata kunci untuk mencari artikel, sutradara, atau festival.
            @endif
        </p>

        <form method="GET" action="{{ route('search.index') }}" class="mt-4 flex max-w-xl">
            <input
                type="text"
                name="q"
                value="{{ $query }}"
                placeholder="Cari artikel, film, festival..."
                class="flex-1 border border-slate-300 px-4 py-2.5 text-xs rounded-l-xl text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
            >
            <button type="submit" class="bg-[#3A07BB] text-white px-6 py-2.5 text-xs font-medium rounded-r-xl hover:bg-[#2e0594] transition">
                <i class="fas fa-search mr-1.5 text-[11px]"></i> Cari
            </button>
        </form>
    </div>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($articles as $article)
            <article class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm hover:shadow-card-hover hover:border-purple-300 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative overflow-hidden rounded-xl h-48 mb-3.5 bg-slate-100">
                        <a href="{{ route('articles.show', ['article' => $article]) }}">
                            <img
                                src="{{ $article->image_url }}"
                                alt="{{ $article->title }}"
                                loading="lazy" decoding="async"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            >
                        </a>
                        @if ($article->primary_category)
                            <div class="absolute top-3 left-3">
                                <span class="badge-cat text-[10px]">{{ $article->primary_category->name }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="space-y-1.5">
                        <h2 class="font-sans font-medium text-base text-slate-800 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                            <a href="{{ route('articles.show', ['article' => $article]) }}">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-xs text-slate-500 line-clamp-3 font-normal leading-relaxed">
                            {{ $article->excerpt }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 whitespace-nowrap">
                    <span class="truncate max-w-[130px] font-normal text-slate-600">{{ $article->author->name ?? 'Redaksi' }}</span>
                    <time datetime="{{ $article->published_at?->toDateString() }}">
                        {{ $article->published_at?->translatedFormat('d M Y') }}
                    </time>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200/80">
                <i class="fas fa-search text-4xl text-slate-300 mb-3 block"></i>
                <p class="text-xs text-slate-500 font-normal">Tidak ada artikel yang cocok dengan pencarian Anda.</p>
                <a href="{{ route('articles.index') }}" class="inline-block mt-4 text-xs font-medium text-[#3A07BB] hover:underline">
                    &larr; Lihat Semua Artikel
                </a>
            </div>
        @endforelse
    </div>

    @if ($articles->hasPages())
        <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
