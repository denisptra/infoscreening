@extends('layouts.admin')

@section('page_title', 'Content Manager: Articles')

@section('content')
<div class="space-y-6">

    <!-- Strapi Content Manager Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-xl border border-[#eaeaef] shadow-strapi">
        <div>
            <div class="flex items-center gap-2">
                <h2 class="font-sans font-semibold text-lg text-[#32324d]">Articles</h2>
                <span class="text-xs text-[#8e8ea9] font-normal">({{ $articles->total() }} entries found)</span>
            </div>
            <p class="text-xs text-[#666687] font-normal mt-0.5">Manage and publish editorial articles and cinema reviews.</p>
        </div>

        <div class="flex items-center gap-3">
            <a
                href="{{ route('strapi.articles.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm whitespace-nowrap"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create new entry</span>
            </a>
        </div>
    </div>

    <!-- Strapi Filter & Search Bar -->
    <div class="bg-white p-3.5 rounded-xl border border-[#eaeaef] shadow-strapi flex flex-wrap items-center justify-between gap-3 text-xs">
        <form method="GET" action="{{ route('strapi.articles.index') }}" class="flex items-center gap-2 max-w-md w-full">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-2.5 text-[#8e8ea9] text-xs"></i>
                <input
                    type="text"
                    name="q"
                    value="{{ request('q') }}"
                    placeholder="Search by title..."
                    class="w-full pl-8 pr-3 py-1.5 text-xs border border-[#dcdce4] rounded-md focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-[#32324d]"
                >
            </div>
            <select name="status" class="text-xs border border-[#dcdce4] rounded-md px-2.5 py-1.5 text-[#32324d]">
                <option value="">All Statuses</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
            <button type="submit" class="px-3.5 py-1.5 bg-[#f6f6f9] hover:bg-[#eaeaef] text-[#32324d] font-medium rounded-md transition">
                Filters
            </button>
        </form>

        <span class="text-[11px] text-[#8e8ea9]">
            Showing {{ $articles->count() }} of {{ $articles->total() }} entries
        </span>
    </div>

    <!-- Strapi Table View -->
    <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-[#f6f6f9] text-[#666687] font-semibold text-[11px] uppercase tracking-wider border-b border-[#eaeaef]">
                    <tr>
                        <th class="p-3.5 pl-5">Title</th>
                        <th class="p-3.5">Category</th>
                        <th class="p-3.5">Status</th>
                        <th class="p-3.5">Featured Hero</th>
                        <th class="p-3.5 text-center">Views</th>
                        <th class="p-3.5 text-right pr-5">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[#eaeaef]">
                    @forelse ($articles as $article)
                        <tr class="hover:bg-[#f6f6f9]/80 transition">
                            <td class="p-3.5 pl-5">
                                <div class="flex items-center gap-3">
                                    <img loading="lazy" decoding="async" src="{{ $article->image_url }}" alt="" class="w-10 h-10 rounded-lg object-cover shrink-0 border border-slate-200">
                                    <div class="min-w-0">
                                        <a href="{{ route('strapi.articles.edit', $article) }}" class="font-medium text-[#32324d] hover:text-[#4945ff] line-clamp-1">
                                            {{ $article->title }}
                                        </a>
                                        <div class="flex items-center gap-2 text-[10px] text-[#8e8ea9] mt-0.5 whitespace-nowrap">
                                            <span>Author: {{ $article->author->name ?? 'Redaksi' }}</span>
                                            <span>&bull;</span>
                                            <time>{{ $article->published_at?->translatedFormat('d M Y') ?? 'Unpublished' }}</time>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                <div class="flex flex-wrap gap-1">
                                    @foreach ($article->categories as $cat)
                                        <span class="text-[10px] font-medium text-[#4945ff] bg-[#f0ebfc] px-2 py-0.5 rounded">
                                            {{ $cat->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($article->status === 'published')
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-medium text-emerald-700">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Published
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-normal text-slate-400">
                                        <span class="w-2 h-2 rounded-full bg-slate-400"></span> Draft
                                    </span>
                                @endif
                            </td>
                            <td class="p-3.5 whitespace-nowrap">
                                @if ($article->is_featured)
                                    <span class="text-[10px] font-semibold text-amber-700 bg-amber-50 px-2 py-0.5 rounded border border-amber-200">
                                        <i class="fas fa-star text-amber-500 mr-0.5"></i> Featured
                                    </span>
                                @else
                                    <span class="text-[#dcdce4]">-</span>
                                @endif
                            </td>
                            <td class="p-3.5 text-center font-normal text-slate-600 whitespace-nowrap">
                                {{ number_format($article->views) }}
                            </td>
                            <td class="p-3.5 text-right pr-5 space-x-2 whitespace-nowrap">
                                <a href="{{ route('strapi.articles.edit', $article) }}" class="p-1 text-[#666687] hover:text-[#4945ff]" title="Edit Entry">
                                    <i class="far fa-edit"></i>
                                </a>
                                <a href="{{ route('articles.show', ['article' => $article]) }}" target="_blank" class="p-1 text-[#8e8ea9] hover:text-[#32324d]" title="View Live">
                                    <i class="fas fa-arrow-up-right-from-square text-[11px]"></i>
                                </a>
                                <form method="POST" action="{{ route('strapi.articles.destroy', $article) }}" class="inline" data-confirm data-confirm-title="Hapus artikel?" data-confirm-message="Artikel ini akan dihapus permanen dan tidak bisa dipulihkan." data-confirm-action="Hapus artikel">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1 text-[#8e8ea9] hover:text-red-600" title="Delete">
                                        <i class="far fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="p-8 text-center text-[#8e8ea9]">
                                No article entries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($articles->hasPages())
            <div class="p-4 border-t border-[#eaeaef]">
                {{ $articles->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
