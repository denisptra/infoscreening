@extends('layouts.admin')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 rounded-xl border border-[#eaeaef] bg-white p-5 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-[#32324d]">Dashboard Admin</h1>
            <p class="mt-1 text-xs text-[#666687]">Ringkasan konten dan aktivitas utama Infoscreening.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('strapi.articles.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-[#4945ff] px-4 py-2 text-xs font-medium text-white hover:bg-[#3b37eb]">
                <i class="fas fa-plus text-[10px]"></i><span>Tulis Artikel</span>
            </a>
            <a href="{{ route('strapi.navigation.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-[#dcdce4] px-4 py-2 text-xs font-medium text-[#32324d] hover:border-[#4945ff] hover:text-[#4945ff]">
                <i class="fas fa-bars-staggered text-[10px]"></i><span>Kelola Navigasi</span>
            </a>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-[#eaeaef] bg-white p-4">
            <p class="text-xs text-[#666687]">Artikel</p>
            <p class="mt-2 text-2xl font-semibold text-[#32324d]">{{ number_format($stats['articles']) }}</p>
            <p class="mt-1 text-[11px] text-[#8e8ea9]"><span class="font-medium text-emerald-600">{{ $stats['published'] }}</span> terbit · {{ $stats['drafts'] }} draf</p>
        </div>
        <div class="rounded-xl border border-[#eaeaef] bg-white p-4">
            <p class="text-xs text-[#666687]">Agenda Mendatang</p>
            <p class="mt-2 text-2xl font-semibold text-[#32324d]">{{ number_format($stats['events']) }}</p>
            <p class="mt-1 text-[11px] text-[#8e8ea9]">Event / pemutaran aktif</p>
        </div>
        <div class="rounded-xl border border-[#eaeaef] bg-white p-4">
            <p class="text-xs text-[#666687]">Kontribusi Menunggu</p>
            <p class="mt-2 text-2xl font-semibold {{ $stats['pending'] ? 'text-amber-600' : 'text-[#32324d]' }}">{{ number_format($stats['pending']) }}</p>
            <p class="mt-1 text-[11px] text-[#8e8ea9]">Perlu tinjauan redaksi</p>
        </div>
        <div class="rounded-xl border border-[#eaeaef] bg-white p-4">
            <p class="text-xs text-[#666687]">Pengguna CMS</p>
            <p class="mt-2 text-2xl font-semibold text-[#32324d]">{{ number_format($stats['users']) }}</p>
            <p class="mt-1 text-[11px] text-[#8e8ea9]">{{ $stats['admins'] }} admin · {{ $stats['editors'] }} editor</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <a href="{{ route('strapi.articles.index') }}" class="flex items-center gap-3 rounded-xl border border-[#eaeaef] bg-white p-4 hover:border-[#4945ff]/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f0ebfc] text-[#4945ff]"><i class="far fa-newspaper"></i></span>
            <span><span class="block text-xs font-semibold text-[#32324d]">Kelola Artikel</span><span class="block text-[11px] text-[#8e8ea9]">{{ $stats['articles'] }} entri</span></span>
        </a>
        <a href="{{ route('strapi.categories.index') }}" class="flex items-center gap-3 rounded-xl border border-[#eaeaef] bg-white p-4 hover:border-[#4945ff]/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-amber-50 text-amber-600"><i class="fas fa-folder"></i></span>
            <span><span class="block text-xs font-semibold text-[#32324d]">Kategori / Rubrik</span><span class="block text-[11px] text-[#8e8ea9]">{{ $stats['categories'] }} rubrik</span></span>
        </a>
        <a href="{{ route('strapi.media.index') }}" class="flex items-center gap-3 rounded-xl border border-[#eaeaef] bg-white p-4 hover:border-[#4945ff]/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-pink-50 text-pink-600"><i class="fas fa-images"></i></span>
            <span><span class="block text-xs font-semibold text-[#32324d]">Media Library</span><span class="block text-[11px] text-[#8e8ea9]">{{ $stats['media'] }} aset</span></span>
        </a>
        <a href="{{ route('strapi.settings.index', ['tab' => 'users']) }}" class="flex items-center gap-3 rounded-xl border border-[#eaeaef] bg-white p-4 hover:border-[#4945ff]/50">
            <span class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"><i class="fas fa-users-gear"></i></span>
            <span><span class="block text-xs font-semibold text-[#32324d]">Pengguna &amp; Peran</span><span class="block text-[11px] text-[#8e8ea9]">Atur akses tim</span></span>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <section class="overflow-hidden rounded-xl border border-[#eaeaef] bg-white xl:col-span-2">
            <div class="flex items-center justify-between border-b border-[#eaeaef] p-4 sm:p-5">
                <div>
                    <h2 class="text-sm font-semibold text-[#32324d]">Artikel Terbaru</h2>
                    <p class="mt-0.5 text-[11px] text-[#8e8ea9]">Entri artikel terbaru di CMS.</p>
                </div>
                <a href="{{ route('strapi.articles.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline">Lihat semua</a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="border-b border-[#eaeaef] bg-[#f6f6f9] text-[11px] uppercase tracking-wider text-[#666687]">
                        <tr>
                            <th class="p-3 pl-5 font-semibold">Judul</th>
                            <th class="p-3 font-semibold">Kategori</th>
                            <th class="p-3 font-semibold">Status</th>
                            <th class="p-3 pr-5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[#eaeaef]">
                        @forelse ($recentArticles as $art)
                            <tr class="hover:bg-[#fafafc]">
                                <td class="p-3 pl-5">
                                    <a href="{{ route('strapi.articles.edit', $art) }}" class="font-medium text-[#32324d] hover:text-[#4945ff]">{{ $art->title }}</a>
                                    <div class="mt-0.5 text-[10px] text-[#8e8ea9]">{{ $art->updated_at->diffForHumans() }}</div>
                                </td>
                                <td class="p-3 text-[#666687]">{{ $art->primary_category?->name ?? '-' }}</td>
                                <td class="p-3">
                                    @if ($art->status === 'published')
                                        <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700"><span class="h-2 w-2 rounded-full bg-emerald-500"></span>Terbit</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[11px] text-slate-500"><span class="h-2 w-2 rounded-full bg-slate-400"></span>Draf</span>
                                    @endif
                                </td>
                                <td class="p-3 pr-5 text-right">
                                    <a href="{{ route('strapi.articles.edit', $art) }}" class="text-[#4945ff] hover:underline">Edit</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-6 text-center text-[#8e8ea9]">Belum ada artikel.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <div class="space-y-6">
            <section class="rounded-xl border border-[#eaeaef] bg-white p-4 sm:p-5">
                <div class="flex items-center justify-between border-b border-[#eaeaef] pb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-[#32324d]">Rubrik Artikel</h2>
                        <p class="mt-0.5 text-[11px] text-[#8e8ea9]">Kategori dan menu rubrikasi situs.</p>
                    </div>
                    <a href="{{ route('strapi.categories.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline">Kelola</a>
                </div>
                <ul class="mt-3 divide-y divide-[#f0f0f4] text-xs">
                    @forelse ($dashboardCategories as $cat)
                        <li class="flex items-center justify-between py-2">
                            <a href="{{ route('categories.show', ['category' => $cat]) }}" target="_blank" class="font-medium text-[#32324d] hover:text-[#4945ff] truncate">
                                {{ $cat->menu_label ?? $cat->name }}
                            </a>
                            <span class="shrink-0 rounded-full bg-[#f6f6f9] px-2 py-0.5 text-[11px] font-mono tabular-nums text-[#666687]">{{ $cat->articles_count }}</span>
                        </li>
                    @empty
                        <li class="py-4 text-center text-[#8e8ea9]">Belum ada rubrik.</li>
                    @endforelse
                </ul>
            </section>

            <section class="rounded-xl border border-[#eaeaef] bg-white p-4 sm:p-5">
                <div class="flex items-center justify-between border-b border-[#eaeaef] pb-3">
                    <div>
                        <h2 class="text-sm font-semibold text-[#32324d]">Topik Pilihan</h2>
                        <p class="mt-0.5 text-[11px] text-[#8e8ea9]">Tag populer di portal.</p>
                    </div>
                    <a href="{{ route('strapi.tags.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline">Kelola</a>
                </div>
                <div class="mt-3 flex flex-wrap gap-1.5">
                    @forelse ($dashboardTags as $tag)
                        <a href="{{ route('tags.show', ['tag' => $tag]) }}" target="_blank" class="inline-flex items-center gap-1 rounded-full border border-[#eaeaef] bg-[#fafafc] px-2.5 py-1 text-[11px] text-[#32324d] hover:border-[#4945ff] hover:text-[#4945ff]">
                            <span>#{{ $tag->name }}</span>
                            <span class="text-[10px] text-[#8e8ea9]">({{ $tag->articles_count }})</span>
                        </a>
                    @empty
                        <p class="text-xs text-[#8e8ea9]">Belum ada topik.</p>
                    @endforelse
                </div>
            </section>

            @if ($pendingContributions->isNotEmpty())
                <section class="rounded-xl border border-[#eaeaef] bg-white p-4 sm:p-5">
                    <div class="flex items-center justify-between border-b border-[#eaeaef] pb-3">
                        <h2 class="text-sm font-semibold text-[#32324d]">Kontribusi Menunggu</h2>
                        <a href="{{ route('strapi.contributions.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline">Semua</a>
                    </div>
                    <ul class="mt-3 space-y-2.5 text-xs">
                        @foreach ($pendingContributions as $contrib)
                            <li class="flex items-start justify-between gap-2">
                                <a href="{{ route('strapi.contributions.show', $contrib) }}" class="font-medium text-[#32324d] hover:text-[#4945ff] truncate">{{ $contrib->title }}</a>
                                <span class="shrink-0 text-[10px] text-[#8e8ea9]">{{ $contrib->name }}</span>
                            </li>
                        @endforeach
                    </ul>
                </section>
            @endif
        </div>
    </div>
</div>
@endsection
