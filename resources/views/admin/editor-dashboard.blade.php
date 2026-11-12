@extends('layouts.admin')

@section('page_title', 'Dashboard Redaksi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-xl border border-[#eaeaef] bg-white p-6 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-[#32324d]">Dashboard Redaksi</h1>
            <p class="mt-1 text-xs text-[#666687]">
                @if ($role->value === 'editor')
                    Tulis dan kelola draf artikel Anda. Admin atau Super Admin akan menerbitkannya.
                @else
                    Kelola seluruh artikel berita dan terbitkan informasi terbaru. Pengaturan situs tetap dikelola Super Admin.
                @endif
            </p>
        </div>
        <a href="{{ route('strapi.articles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#4945ff] px-4 py-2 text-xs font-medium text-white hover:bg-[#3b37eb]">
            <i class="fas fa-plus text-[10px]"></i>
            <span>Tulis Artikel</span>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        @foreach ([
            ['label' => 'Artikel', 'value' => $stats['articles'], 'icon' => 'fa-newspaper'],
            ['label' => 'Terbit', 'value' => $stats['published'], 'icon' => 'fa-circle-check'],
            ['label' => 'Draf', 'value' => $stats['drafts'], 'icon' => 'fa-file-lines'],
        ] as $stat)
            <div class="flex items-center justify-between rounded-xl border border-[#eaeaef] bg-white p-5">
                <div>
                    <p class="text-xs text-[#666687]">{{ $stat['label'] }}</p>
                    <p class="mt-2 text-2xl font-semibold text-[#32324d]">{{ number_format($stat['value']) }}</p>
                </div>
                <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f0ebfc] text-[#4945ff]">
                    <i class="fas {{ $stat['icon'] }}"></i>
                </span>
            </div>
        @endforeach
    </div>

    @include('admin.partials.role-access')

    <section class="overflow-hidden rounded-xl border border-[#eaeaef] bg-white">
        <div class="flex items-center justify-between border-b border-[#eaeaef] p-5">
            <div>
                <h2 class="text-sm font-semibold text-[#32324d]">Artikel Terbaru</h2>
                <p class="mt-1 text-xs text-[#8e8ea9]">Draf dan artikel yang sudah diterbitkan.</p>
            </div>
            <a href="{{ route('strapi.articles.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline">Semua Artikel</a>
        </div>
        <div class="divide-y divide-[#eaeaef]">
            @forelse ($recentArticles as $article)
                <a href="{{ route('strapi.articles.edit', $article) }}" class="flex items-center justify-between gap-4 p-4 hover:bg-[#fafafc]">
                    <div class="min-w-0">
                        <p class="truncate text-xs font-medium text-[#32324d]">{{ $article->title }}</p>
                        <p class="mt-1 text-[11px] text-[#8e8ea9]">{{ $article->categories->pluck('name')->join(', ') ?: 'Tanpa kategori' }}</p>
                    </div>
                    <span class="shrink-0 rounded-full px-2.5 py-1 text-[10px] {{ $article->status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-slate-100 text-slate-600' }}">
                        {{ $article->status === 'published' ? 'Terbit' : 'Draf' }}
                    </span>
                </a>
            @empty
                <div class="p-8 text-center text-xs text-[#8e8ea9]">
                    Belum ada artikel. Mulai dengan menulis artikel pertama.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection
