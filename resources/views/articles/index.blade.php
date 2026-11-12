@extends('layouts.site')

@section('title', 'Arsip Artikel & Berita - Infoscreening')
@section('description', 'Kumpulan artikel, kabar terkini, wawancara, dan esai seputar sinema alternatif dan festival film di Indonesia.')

@section('content')
<div class="space-y-8">
    <!-- Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
                <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Beranda</a>
                <i class="fas fa-angle-right text-[9px]"></i>
                <span class="text-[#3A07BB] font-medium">Artikel</span>
            </nav>
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-800 tracking-tight">
                Arsip Artikel &amp; Berita
            </h1>
            <p class="text-xs text-slate-500 mt-1 max-w-xl font-normal leading-relaxed">
                Jelajahi wawasan mendalam, catatan festival, ulasan film alternatif, dan wawancara eksklusif sineas.
            </p>
        </div>

        <div class="text-xs text-slate-500 font-normal">
            Total <strong>{{ $articles->total() }}</strong> artikel terbit
        </div>
    </div>

    <!-- Articles Grid (Upgraded Cards) -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($articles as $article)
            <article class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm hover:shadow-card-hover hover:border-purple-300 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative overflow-hidden rounded-xl h-48 mb-3.5 bg-slate-100">
                        <a href="{{ route('articles.show', ['article' => $article]) }}">
                            <img loading="lazy" decoding="async"
                                src="{{ $article->image_url }}"
                                alt="{{ $article->title }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                            >
                        </a>
                        @if ($article->primary_category)
                            <div class="absolute top-3 left-3">
                                <span class="badge-cat text-[10px]">
                                    {{ $article->primary_category->name }}
                                </span>
                            </div>
                        @endif
                        <div class="absolute bottom-2.5 right-2.5 bg-black/70 backdrop-blur-sm text-white text-[10px] px-2.5 py-0.5 rounded-md flex items-center gap-1 whitespace-nowrap">
                            <i class="far fa-eye text-purple-300"></i>
                            <span>{{ number_format($article->views) }}</span>
                        </div>
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
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200 text-slate-400">
                <i class="far fa-newspaper text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs">Belum ada artikel yang diterbitkan.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
        {{ $articles->links() }}
    </div>
</div>
@endsection
