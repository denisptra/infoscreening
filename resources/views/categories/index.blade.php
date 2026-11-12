@extends('layouts.site')

@section('title', 'Daftar Kategori - Infoscreening')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
            Kategori Liputan
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-1 font-normal">
            Temukan artikel berdasarkan bidang tema dan fokus liputan Infoscreening.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach ($categories as $category)
            <a href="{{ route('categories.show', ['category' => $category]) }}" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-card hover:border-[#3A07BB]/30 hover:shadow-card-hover transition duration-300 block group">
                <div class="flex items-center justify-between mb-3">
                    <span class="badge-cat text-[11px]">{{ $category->name }}</span>
                    <span class="text-xs text-slate-400 font-normal">{{ $category->articles_count ?? 0 }} artikel</span>
                </div>
                <h2 class="font-sans font-semibold text-lg text-slate-900 group-hover:text-[#3A07BB] transition">
                    {{ $category->name }}
                </h2>
                <p class="text-xs text-slate-600 mt-2 line-clamp-2 leading-relaxed font-normal">
                    {{ $category->description ?? 'Jelajahi berita dan ulasan seputar ' . $category->name . '.' }}
                </p>
                <div class="mt-4 text-xs font-medium text-[#3A07BB] flex items-center gap-1.5">
                    <span>Buka Kategori</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </div>
            </a>
        @endforeach
    </div>
</div>
@endsection
