@extends('layouts.site')

@section('title', 'Daftar Penulis (Authors List) - Infoscreening')
@section('description', 'Sineas, kurator, dan jurnalis yang aktif menuliskan catatan perjalanan sinema di Infoscreening.')

@section('content')
<div class="space-y-8">
    <!-- Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm">
        <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
            <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Beranda</a>
            <i class="fas fa-angle-right text-[9px]"></i>
            <span class="text-[#3A07BB] font-medium">Authors List</span>
        </nav>
        <span class="badge-cat mb-2">Tim Redaksi</span>
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-800 tracking-tight">
            Daftar Penulis &amp; Kontributor
        </h1>
        <p class="text-xs text-slate-500 mt-1 max-w-2xl font-normal leading-relaxed">
            Sineas, kurator, dan jurnalis yang aktif menuliskan catatan perjalanan sinema di Infoscreening.
        </p>
    </div>

    <!-- Authors Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($authors as $author)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-card-hover hover:border-purple-300 transition-all duration-300 flex items-start gap-4">
                <div class="w-14 h-14 rounded-full bg-[#3A07BB] text-white flex items-center justify-center font-sans font-semibold text-xl shrink-0">
                    {{ substr($author->name, 0, 1) }}
                </div>
                <div class="space-y-1 min-w-0">
                    <h3 class="font-sans font-semibold text-base text-slate-900 truncate">{{ $author->name }}</h3>
                    <p class="text-xs text-[#3A07BB] font-medium">{{ $author->articles_count }} Artikel Diterbitkan</p>
                    <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-normal">
                        Editor dan kontributor sinema alternatif Infoscreening.
                    </p>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200/80">
                <i class="fas fa-users text-4xl text-slate-300 mb-3 block"></i>
                <p class="text-xs text-slate-500 font-normal">Belum ada daftar penulis.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
