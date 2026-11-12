@extends('layouts.site')

@section('title', 'Kategori: ' . $category->name . ' - Infoscreening')
@section('description', $category->description ?? 'Arsip artikel kategori ' . $category->name . ' di Infoscreening.')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
            <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Home</a>
            <i class="fas fa-angle-right text-[10px]"></i>
            <a href="{{ route('categories.index') }}" class="hover:text-[#3A07BB]">Kategori</a>
            <i class="fas fa-angle-right text-[10px]"></i>
            <span class="text-[#3A07BB] font-medium">{{ $category->name }}</span>
        </nav>
        <span class="badge-cat mb-2">Kategori</span>
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
            {{ $category->name }}
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-2xl leading-relaxed font-normal">
            {{ $category->description ?? 'Kumpulan berita, liputan, dan artikel kurasi seputar ' . $category->name . '.' }}
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($articles as $article)
            <article class="bg-white border border-slate-200/80 hover:border-[#3A07BB]/30 rounded-2xl shadow-card hover:shadow-card-hover transition duration-300 flex flex-col justify-between overflow-hidden group">
                <div>
                    <div class="relative overflow-hidden aspect-video bg-slate-100">
                        <a href="{{ route('articles.show', ['article' => $article]) }}">
                            <img loading="lazy" decoding="async" src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                        <div class="absolute bottom-2.5 right-2.5 bg-black/60 backdrop-blur-xs text-white text-[10px] font-normal px-2 py-0.5 rounded-md flex items-center gap-1">
                            <i class="far fa-eye text-[9px]"></i>
                            <span>{{ number_format($article->views) }}</span>
                        </div>
                    </div>

                    <div class="p-5 space-y-2">
                        <h2 class="font-sans font-semibold text-sm sm:text-base text-slate-900 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                            <a href="{{ route('articles.show', ['article' => $article]) }}">
                                {{ $article->title }}
                            </a>
                        </h2>
                        <p class="text-xs text-slate-500 line-clamp-3 leading-relaxed font-normal">
                            {{ $article->excerpt }}
                        </p>
                    </div>
                </div>

                <div class="p-5 pt-0">
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 font-normal whitespace-nowrap">
                        <span>{{ $article->author->name ?? 'Redaksi' }}</span>
                        <time>{{ $article->published_at?->translatedFormat('d M Y') }}</time>
                    </div>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200/80">
                <i class="far fa-folder-open text-4xl text-slate-300 mb-3 block"></i>
                <p class="text-xs text-slate-500 font-normal">Belum ada artikel dalam kategori ini.</p>
            </div>
        @endforelse
    </div>

    @if ($articles->hasPages())
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-card">
            {{ $articles->links() }}
        </div>
    @endif
</div>
@endsection
