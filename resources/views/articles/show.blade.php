@extends('layouts.site')

@section('title', ($article->meta_title ?? $article->title) . ' - Infoscreening')
@section('description', $article->meta_description ?? Str::limit(strip_tags($article->excerpt ?? $article->content), 155))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <!-- Main Article Body (8 cols) -->
    <article class="lg:col-span-8 bg-white p-6 sm:p-9 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
        <!-- Breadcrumb -->
        <nav class="text-xs text-slate-400 flex items-center gap-1.5 flex-wrap font-normal">
            <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Beranda</a>
            <i class="fas fa-angle-right text-[9px]"></i>
            <a href="{{ route('articles.index') }}" class="hover:text-[#3A07BB]">Artikel</a>
            <i class="fas fa-angle-right text-[9px]"></i>
            @if ($article->primary_category)
                <span class="text-[#3A07BB]">{{ $article->primary_category->name }}</span>
                <i class="fas fa-angle-right text-[9px]"></i>
            @endif
            <span class="text-slate-400 truncate max-w-xs">{{ $article->title }}</span>
        </nav>

        <!-- Category & Title -->
        <div class="space-y-3">
            @if ($article->primary_category)
                <span class="badge-cat text-[11px]">
                    {{ $article->primary_category->name }}
                </span>
            @endif

            <h1 class="font-sans font-semibold text-2xl sm:text-3xl lg:text-4xl text-slate-900 leading-snug tracking-tight">
                {{ $article->title }}
            </h1>
        </div>

        <!-- Metadata Bar (Horizontal, Nyamping) -->
        <div class="flex flex-wrap items-center justify-between gap-3 text-xs text-slate-500 py-3 border-y border-slate-100 font-normal">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-full bg-[#3A07BB] text-white flex items-center justify-center font-medium text-xs">
                    {{ substr($article->legacy_author_name ?: ($article->author->name ?? 'R'), 0, 1) }}
                </div>
                <span class="font-medium text-slate-700">{{ $article->legacy_author_name ?: ($article->author->name ?? 'Redaksi Infoscreening') }}</span>
            </div>

            <div class="flex items-center gap-3 whitespace-nowrap text-slate-400">
                <span><i class="far fa-calendar mr-1 text-[#3A07BB]"></i>{{ $article->published_at?->translatedFormat('d M Y') }}</span>
                <span>&bull;</span>
                <span><i class="far fa-eye mr-1 text-[#3A07BB]"></i>{{ number_format($article->views) }} views</span>
                <span>&bull;</span>
                <span><i class="far fa-clock mr-1 text-[#3A07BB]"></i>{{ $article->reading_time }} menit baca</span>
            </div>
        </div>

        <!-- Featured Image -->
        @if ($article->image_url)
            <div class="overflow-hidden rounded-xl border border-slate-200/60 shadow-xs">
                <img
                    src="{{ $article->image_url }}"
                    alt="{{ $article->title }}"
                    decoding="async"
                    fetchpriority="high"
                    class="w-full max-h-[480px] object-cover"
                >
            </div>
        @endif

        <!-- Excerpt Callout -->
        @if ($article->excerpt)
            <div class="bg-purple-50/70 border-l-3 border-[#3A07BB] p-4 rounded-r-lg text-xs sm:text-sm text-slate-700 font-normal leading-relaxed italic">
                {{ $article->excerpt }}
            </div>
        @endif

        <!-- Content Body (Inter Normal) -->
        <div class="article-content text-slate-700 text-sm sm:text-[15px] leading-relaxed space-y-4 font-normal">
            @if (strip_tags($article->content) !== $article->content)
                {!! $article->content !!}
            @else
                {!! nl2br(e($article->content)) !!}
            @endif
        </div>

        <!-- Tags -->
        @if ($article->tags->isNotEmpty())
            <div class="pt-6 border-t border-slate-100 flex items-center gap-2 flex-wrap">
                <span class="text-xs text-slate-400 mr-1"><i class="fas fa-tags text-[#3A07BB] mr-1"></i> Topik:</span>
                @foreach ($article->tags as $tag)
                    <a href="{{ route('tags.show', ['tag' => $tag]) }}" class="text-xs bg-slate-100 hover:bg-[#3A07BB] hover:text-white text-slate-600 px-3 py-1 rounded-md transition font-normal whitespace-nowrap">
                        #{{ $tag->name }}
                    </a>
                @endforeach
            </div>
        @endif

        <!-- Social Share Bar -->
        <div class="pt-4 border-t border-slate-100 flex items-center justify-between flex-wrap gap-3">
            <span class="text-xs text-slate-500 font-normal">Bagikan tulisan ini:</span>
            <div class="flex items-center gap-2">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-7 h-7 rounded-lg bg-[#1877F2] text-white flex items-center justify-center text-xs hover:opacity-90">
                    <i class="fab fa-facebook-f text-[10px]"></i>
                </a>
                <a href="https://twitter.com/intent/tweet?text={{ urlencode($article->title) }}&url={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-7 h-7 rounded-lg bg-black text-white flex items-center justify-center text-xs hover:opacity-90">
                    <i class="fab fa-x-twitter text-[10px]"></i>
                </a>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($article->title . ' ' . request()->fullUrl()) }}" target="_blank" class="w-7 h-7 rounded-lg bg-[#25D366] text-white flex items-center justify-center text-xs hover:opacity-90">
                    <i class="fab fa-whatsapp text-[11px]"></i>
                </a>
            </div>
        </div>
    </article>

    <!-- Sidebar: Related Articles (4 cols) -->
    <aside class="lg:col-span-4 space-y-6">
        @if ($related->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-sans font-semibold text-sm text-slate-800">
                        Artikel Terkait
                    </h3>
                </div>
                <div class="space-y-3.5">
                    @foreach ($related as $rel)
                        <article class="flex gap-3 group">
                            <a href="{{ route('articles.show', ['article' => $rel]) }}" class="shrink-0 w-20 h-16 overflow-hidden rounded-lg bg-slate-100 border border-slate-200/60">
                                <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            </a>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-sans font-normal text-xs text-slate-800 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                                    <a href="{{ route('articles.show', ['article' => $rel]) }}">
                                        {{ $rel->title }}
                                    </a>
                                </h4>
                                <time class="text-[10px] text-slate-400 mt-1 block">
                                    {{ $rel->published_at?->translatedFormat('d M Y') }}
                                </time>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="rounded-2xl bg-purple-50/60 border border-purple-200 p-5 text-center space-y-2">
            <p class="text-xs text-slate-600 font-normal">Tertarik membaca ulasan dan liputan lainnya?</p>
            <a href="{{ route('articles.index') }}" class="inline-block px-4 py-2 bg-[#3A07BB] hover:bg-[#2e0594] text-white text-xs font-medium rounded-lg transition shadow-xs">
                Jelajahi Semua Arsip
            </a>
        </div>
    </aside>

</div>
@endsection
