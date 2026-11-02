@extends('layouts.admin')

@section('page_title', 'Overview Dashboard')

@section('content')
<div class="space-y-6">

    <!-- 1. Strapi Iconic Welcome Card Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-[#212134] via-[#2a2942] to-[#3A07BB] text-white p-6 sm:p-7 rounded-xl shadow-strapi-card border border-[#32324d]">
        <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-5">
            <div class="space-y-1.5 max-w-xl">
                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-white/10 text-purple-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    <span>Infoscreening Headless CMS</span>
                </div>
                <h2 class="font-sans font-semibold text-xl sm:text-2xl text-white tracking-tight">
                    Welcome to Infoscreening CMS 👋
                </h2>
                <p class="text-xs text-[#c0c0cf] font-normal leading-relaxed">
                    Kelola seluruh ekosistem sinema alternatif, kalender festival film, naskah liputan, dan direktori kurator secara terpusat.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2.5 shrink-0">
                <a
                    href="{{ route('strapi.articles.create') }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm"
                >
                    <i class="fas fa-plus text-[10px]"></i>
                    <span>Create an Entry</span>
                </a>
                <a
                    href="{{ route('strapi.media.index') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-xs font-normal border border-white/15 transition"
                >
                    <i class="fas fa-images text-[10px]"></i>
                    <span>Media Library</span>
                </a>
                <a
                    href="{{ route('strapi.events.create') }}"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white/10 hover:bg-white/15 text-white text-xs font-normal border border-white/15 transition"
                >
                    <i class="far fa-calendar-plus text-[10px]"></i>
                    <span>New Event</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. Strapi Collection Metrics KPI Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Metric 1: Articles -->
        <div class="bg-white p-4.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#666687]">Articles</span>
                <div class="w-8 h-8 rounded-lg bg-[#f0ebfc] text-[#3A07BB] flex items-center justify-center text-xs">
                    <i class="far fa-newspaper"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-sans font-semibold text-2xl text-[#32324d]">{{ number_format($stats['articles']) }}</h3>
                <p class="text-[11px] text-[#8e8ea9] mt-0.5 font-normal">
                    <span class="text-emerald-600 font-medium">{{ $stats['published'] }} Published</span> &bull; {{ $stats['drafts'] }} Drafts
                </p>
            </div>
        </div>

        <!-- Metric 2: Views -->
        <div class="bg-white p-4.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#666687]">Total Page Views</span>
                <div class="w-8 h-8 rounded-lg bg-[#e8fbf3] text-emerald-600 flex items-center justify-center text-xs">
                    <i class="far fa-eye"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-sans font-semibold text-2xl text-[#32324d]">{{ number_format($stats['views']) }}</h3>
                <p class="text-[11px] text-emerald-600 mt-0.5 font-medium">
                    <i class="fas fa-arrow-trend-up mr-0.5"></i> Pembaca akumulatif
                </p>
            </div>
        </div>

        <!-- Metric 3: Events -->
        <div class="bg-white p-4.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#666687]">Upcoming Events</span>
                <div class="w-8 h-8 rounded-lg bg-[#f0ebfc] text-[#4945ff] flex items-center justify-center text-xs">
                    <i class="far fa-calendar-check"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-sans font-semibold text-2xl text-[#32324d]">{{ $stats['events'] }}</h3>
                <p class="text-[11px] text-[#8e8ea9] mt-0.5 font-normal">
                    Agenda festival &amp; pemutaran
                </p>
            </div>
        </div>

        <!-- Metric 4: Submissions -->
        <div class="bg-white p-4.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-xs font-medium text-[#666687]">Pending Submissions</span>
                <div class="w-8 h-8 rounded-lg bg-[#fff8eb] text-amber-600 flex items-center justify-center text-xs">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
            <div class="mt-3">
                <h3 class="font-sans font-semibold text-2xl text-amber-600">{{ $stats['pending'] }}</h3>
                <p class="text-[11px] text-amber-600 mt-0.5 font-medium">
                    Menunggu kurasi redaksi
                </p>
            </div>
        </div>
    </div>

    <!-- 2.5 Strapi Collection Types Quick Hub -->
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="font-sans font-semibold text-sm text-[#32324d]">Content Manager Collections</h3>
                <p class="text-[11px] text-[#8e8ea9] font-normal">Akses langsung kelola data dan buat entri baru pada setiap tipe konten.</p>
            </div>
            <a href="{{ route('strapi.content-types.index') }}" class="text-xs text-[#4945ff] hover:underline font-medium inline-flex items-center gap-1">
                <span>Content-Type Builder</span>
                <i class="fas fa-cubes text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
            <!-- Collection: Articles -->
            <div class="p-3.5 bg-white rounded-xl border border-[#eaeaef] shadow-xs hover:border-[#4945ff]/40 transition flex flex-col justify-between group">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-[#f0ebfc] text-[#4945ff] flex items-center justify-center text-xs mb-2 group-hover:scale-105 transition-transform">
                        <i class="far fa-newspaper"></i>
                    </div>
                    <span class="font-semibold text-xs text-[#32324d] block">Articles</span>
                    <span class="text-[10px] text-[#8e8ea9]">{{ number_format($stats['articles']) }} entries</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#f6f6f9] flex items-center justify-between text-[11px]">
                    <a href="{{ route('strapi.articles.create') }}" class="text-[#4945ff] hover:underline font-medium" title="Create Article">+ New</a>
                    <a href="{{ route('strapi.articles.index') }}" class="text-[#8e8ea9] hover:text-[#32324d]">View &rarr;</a>
                </div>
            </div>

            <!-- Collection: Events -->
            <div class="p-3.5 bg-white rounded-xl border border-[#eaeaef] shadow-xs hover:border-[#4945ff]/40 transition flex flex-col justify-between group">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-[#e8fbf3] text-emerald-600 flex items-center justify-center text-xs mb-2 group-hover:scale-105 transition-transform">
                        <i class="far fa-calendar-alt"></i>
                    </div>
                    <span class="font-semibold text-xs text-[#32324d] block">Events</span>
                    <span class="text-[10px] text-[#8e8ea9]">{{ $stats['events'] }} agenda</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#f6f6f9] flex items-center justify-between text-[11px]">
                    <a href="{{ route('strapi.events.create') }}" class="text-[#4945ff] hover:underline font-medium" title="Add Event">+ New</a>
                    <a href="{{ route('strapi.events.index') }}" class="text-[#8e8ea9] hover:text-[#32324d]">View &rarr;</a>
                </div>
            </div>

            <!-- Collection: Categories -->
            <div class="p-3.5 bg-white rounded-xl border border-[#eaeaef] shadow-xs hover:border-[#4945ff]/40 transition flex flex-col justify-between group">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-[#fff8eb] text-amber-600 flex items-center justify-center text-xs mb-2 group-hover:scale-105 transition-transform">
                        <i class="fas fa-folder"></i>
                    </div>
                    <span class="font-semibold text-xs text-[#32324d] block">Categories</span>
                    <span class="text-[10px] text-[#8e8ea9]">{{ \App\Models\Category::count() }} rubrik</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#f6f6f9] flex items-center justify-between text-[11px]">
                    <a href="{{ route('strapi.categories.create') }}" class="text-[#4945ff] hover:underline font-medium">+ New</a>
                    <a href="{{ route('strapi.categories.index') }}" class="text-[#8e8ea9] hover:text-[#32324d]">View &rarr;</a>
                </div>
            </div>

            <!-- Collection: Experts -->
            <div class="p-3.5 bg-white rounded-xl border border-[#eaeaef] shadow-xs hover:border-[#4945ff]/40 transition flex flex-col justify-between group">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-[#f0ebfc] text-[#3A07BB] flex items-center justify-center text-xs mb-2 group-hover:scale-105 transition-transform">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <span class="font-semibold text-xs text-[#32324d] block">Experts</span>
                    <span class="text-[10px] text-[#8e8ea9]">{{ $stats['experts'] }} profil</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#f6f6f9] flex items-center justify-between text-[11px]">
                    <a href="{{ route('strapi.experts.create') }}" class="text-[#4945ff] hover:underline font-medium">+ New</a>
                    <a href="{{ route('strapi.experts.index') }}" class="text-[#8e8ea9] hover:text-[#32324d]">View &rarr;</a>
                </div>
            </div>

            <!-- Collection: Products -->
            <div class="p-3.5 bg-white rounded-xl border border-[#eaeaef] shadow-xs hover:border-[#4945ff]/40 transition flex flex-col justify-between group">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center text-xs mb-2 group-hover:scale-105 transition-transform">
                        <i class="fas fa-bag-shopping"></i>
                    </div>
                    <span class="font-semibold text-xs text-[#32324d] block">Products</span>
                    <span class="text-[10px] text-[#8e8ea9]">{{ $stats['products'] }} item</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#f6f6f9] flex items-center justify-between text-[11px]">
                    <a href="{{ route('strapi.products.create') }}" class="text-[#4945ff] hover:underline font-medium">+ New</a>
                    <a href="{{ route('strapi.products.index') }}" class="text-[#8e8ea9] hover:text-[#32324d]">View &rarr;</a>
                </div>
            </div>

            <!-- Collection: Media Library -->
            <div class="p-3.5 bg-white rounded-xl border border-[#eaeaef] shadow-xs hover:border-[#4945ff]/40 transition flex flex-col justify-between group">
                <div>
                    <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center text-xs mb-2 group-hover:scale-105 transition-transform">
                        <i class="fas fa-images"></i>
                    </div>
                    <span class="font-semibold text-xs text-[#32324d] block">Media Assets</span>
                    <span class="text-[10px] text-[#8e8ea9]">{{ $stats['media'] }} files</span>
                </div>
                <div class="mt-3 pt-2.5 border-t border-[#f6f6f9] flex items-center justify-between text-[11px]">
                    <a href="{{ route('strapi.media.index') }}" class="text-[#4945ff] hover:underline font-medium">Upload</a>
                    <a href="{{ route('strapi.media.index') }}" class="text-[#8e8ea9] hover:text-[#32324d]">View &rarr;</a>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Strapi Content Table & Information Columns -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Articles Collection Manager (8 cols) -->
        <div class="lg:col-span-8 space-y-6">

            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-[#eaeaef] flex items-center justify-between">
                    <div>
                        <h3 class="font-sans font-semibold text-sm text-[#32324d]">Recent Articles Entries</h3>
                        <p class="text-[11px] text-[#8e8ea9] font-normal">Articles collection type entries in the CMS.</p>
                    </div>
                    <a href="{{ route('strapi.articles.index') }}" class="text-xs text-[#4945ff] hover:underline font-medium">
                        View All Entries &rarr;
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#f6f6f9] text-[#666687] font-semibold text-[11px] uppercase tracking-wider border-b border-[#eaeaef]">
                            <tr>
                                <th class="p-3.5 pl-5">Entry Title</th>
                                <th class="p-3.5">Category</th>
                                <th class="p-3.5">Status</th>
                                <th class="p-3.5 text-center">Views</th>
                                <th class="p-3.5 text-right pr-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eaeaef]">
                            @foreach ($recentArticles as $art)
                                <tr class="hover:bg-[#f6f6f9]/80 transition">
                                    <td class="p-3.5 pl-5">
                                        <div class="flex items-center gap-3">
                                            <img loading="lazy" decoding="async" src="{{ $art->image_url }}" alt="" class="w-9 h-9 rounded-md object-cover shrink-0 border border-slate-200">
                                            <div class="min-w-0">
                                                <a href="{{ route('strapi.articles.edit', $art) }}" class="font-medium text-slate-800 hover:text-[#4945ff] line-clamp-1">
                                                    {{ $art->title }}
                                                </a>
                                                <span class="text-[10px] text-[#8e8ea9] font-normal">Updated {{ $art->updated_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3.5">
                                        @if($art->primary_category)
                                            <span class="text-[10px] font-medium text-[#4945ff] bg-[#f0ebfc] px-2 py-0.5 rounded">
                                                {{ $art->primary_category->name }}
                                            </span>
                                        @else
                                            <span class="text-[#8e8ea9]">-</span>
                                        @endif
                                    </td>
                                    <td class="p-3.5">
                                        @if ($art->status === 'published')
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-[11px] font-normal text-slate-400">
                                                <span class="w-2 h-2 rounded-full bg-slate-400"></span> Draft
                                            </span>
                                        @endif
                                    </td>
                                    <td class="p-3.5 text-center text-slate-600 font-normal">
                                        {{ number_format($art->views) }}
                                    </td>
                                    <td class="p-3.5 text-right pr-5 space-x-2">
                                        <a href="{{ route('strapi.articles.edit', $art) }}" class="text-[#666687] hover:text-[#4945ff] p-1" title="Edit Entry">
                                            <i class="far fa-edit"></i>
                                        </a>
                                        <a href="{{ route('articles.show', ['article' => $art]) }}" target="_blank" class="text-[#8e8ea9] hover:text-slate-800 p-1" title="Preview Live">
                                            <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pending Community Submissions Card -->
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                <div class="p-4 sm:p-5 border-b border-[#eaeaef] flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <h3 class="font-sans font-semibold text-sm text-[#32324d]">Community Submissions Inbox</h3>
                        @if ($stats['pending'] > 0)
                            <span class="bg-[#f0ebfc] text-[#4945ff] text-[10px] font-semibold px-2 py-0.5 rounded-full">
                                {{ $stats['pending'] }} New
                            </span>
                        @endif
                    </div>
                    <a href="{{ route('strapi.contributions.index') }}" class="text-xs text-[#4945ff] hover:underline font-medium">
                        Open Inbox &rarr;
                    </a>
                </div>

                <div class="divide-y divide-[#eaeaef]">
                    @forelse ($pendingContributions as $c)
                        <div class="p-4 flex items-start justify-between gap-4 hover:bg-[#f6f6f9]/60 transition">
                            <div class="space-y-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-medium text-[#4945ff] bg-[#f0ebfc] px-1.5 py-0.2 rounded">{{ $c->type }}</span>
                                    <span class="text-xs font-medium text-slate-800 truncate">{{ $c->title }}</span>
                                </div>
                                <p class="text-xs text-[#666687] line-clamp-1 font-normal">{{ $c->message }}</p>
                                <span class="text-[10px] text-[#8e8ea9] font-normal">By: <strong>{{ $c->name }}</strong> &bull; {{ $c->created_at->diffForHumans() }}</span>
                            </div>
                            <a href="{{ route('strapi.contributions.show', $c) }}" class="shrink-0 px-3 py-1 bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium rounded-md transition">
                                Review
                            </a>
                        </div>
                    @empty
                        <div class="p-6 text-center text-xs text-[#8e8ea9] font-normal">
                            All incoming submissions have been reviewed.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Right Column: Strapi System Info & Top Stories (4 cols) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- 1. Strapi System Information Card (Authentic Strapi Feel) -->
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi p-5 space-y-3.5">
                <div class="border-b border-[#eaeaef] pb-3 flex items-center justify-between">
                    <h3 class="font-sans font-semibold text-xs uppercase tracking-wider text-[#32324d] flex items-center gap-2">
                        <i class="fas fa-circle-info text-[#4945ff] text-xs"></i>
                        <span>System Information</span>
                    </h3>
                    <span class="w-2 h-2 rounded-full bg-emerald-500" title="Active"></span>
                </div>

                <div class="space-y-2 text-xs font-normal">
                    <div class="flex items-center justify-between py-1 border-b border-[#f6f6f9]">
                        <span class="text-[#666687]">Strapi Engine</span>
                        <span class="font-medium text-[#32324d]">v5.2.0 (Theme)</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#f6f6f9]">
                        <span class="text-[#666687]">Framework</span>
                        <span class="font-medium text-[#32324d]">Laravel 13 &bull; PHP 8.3</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#f6f6f9]">
                        <span class="text-[#666687]">Media Library</span>
                        <span class="font-medium text-[#4945ff]">{{ number_format($stats['media']) }} assets</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#f6f6f9]">
                        <span class="text-[#666687]">Database</span>
                        <span class="font-medium text-[#32324d]">SQLite (Local)</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-[#f6f6f9]">
                        <span class="text-[#666687]">Font Family</span>
                        <span class="font-medium text-[#32324d]">Inter (Weights 400-600)</span>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="text-[#666687]">Brand Theme</span>
                        <span class="inline-flex items-center gap-1 font-medium text-[#3A07BB]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[#3A07BB]"></span>
                            <span>#3A07BB</span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. Most Viewed Entries Card -->
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi p-5 space-y-3">
                <div class="border-b border-[#eaeaef] pb-2.5 flex items-center justify-between">
                    <h3 class="font-sans font-semibold text-xs uppercase tracking-wider text-[#32324d]">
                        Trending Entries
                    </h3>
                    <span class="text-[10px] text-[#8e8ea9]">Views</span>
                </div>

                <div class="space-y-3">
                    @foreach ($topArticles as $index => $top)
                        <div class="flex items-start gap-2.5 group">
                            <span class="w-5 h-5 rounded bg-[#f0ebfc] text-[#4945ff] font-medium text-[10px] flex items-center justify-center shrink-0 mt-0.5">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('strapi.articles.edit', $top) }}" class="text-xs font-normal text-slate-800 hover:text-[#4945ff] line-clamp-1">
                                    {{ $top->title }}
                                </a>
                                <span class="text-[10px] text-emerald-600 font-medium block">
                                    {{ number_format($top->views) }} views
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 3. Strapi Community & Developer Resources -->
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi p-5 space-y-3">
                <div class="border-b border-[#eaeaef] pb-2.5 flex items-center justify-between">
                    <h3 class="font-sans font-semibold text-xs uppercase tracking-wider text-[#32324d] flex items-center gap-1.5">
                        <i class="fas fa-book-open text-[#4945ff]"></i>
                        <span>Developer Resources</span>
                    </h3>
                </div>

                <div class="space-y-2.5 text-xs">
                    <a href="{{ route('strapi.content-types.index') }}" class="p-2.5 rounded-lg bg-[#fafafc] hover:bg-[#f0ebfc] text-[#32324d] hover:text-[#4945ff] flex items-center justify-between transition group">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-cubes text-[#8e8ea9] group-hover:text-[#4945ff]"></i>
                            <span>Content-Type Builder</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-[#8e8ea9]"></i>
                    </a>

                    <a href="{{ route('strapi.settings.index', ['tab' => 'tokens']) }}" class="p-2.5 rounded-lg bg-[#fafafc] hover:bg-[#f0ebfc] text-[#32324d] hover:text-[#4945ff] flex items-center justify-between transition group">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-key text-[#8e8ea9] group-hover:text-[#4945ff]"></i>
                            <span>REST API Tokens</span>
                        </div>
                        <i class="fas fa-chevron-right text-[10px] text-[#8e8ea9]"></i>
                    </a>

                    <a href="{{ route('home') }}" target="_blank" class="p-2.5 rounded-lg bg-[#fafafc] hover:bg-[#f0ebfc] text-[#32324d] hover:text-[#4945ff] flex items-center justify-between transition group">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-globe text-[#8e8ea9] group-hover:text-[#4945ff]"></i>
                            <span>Live Frontend Website</span>
                        </div>
                        <i class="fas fa-arrow-up-right-from-square text-[10px] text-[#8e8ea9]"></i>
                    </a>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
