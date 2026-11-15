@extends('layouts.site')

@section('title', 'Daftar Tag & Topik - Infoscreening')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
            Indeks Topik &amp; Tag
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-1 font-normal">
            Telusuri arsip Infoscreening melalui kata kunci festival, komunitas, kota, dan isu sinema.
        </p>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <div class="flex flex-wrap gap-2">
            @foreach ($tags as $tag)
                <a href="{{ route('tags.show', ['tag' => $tag]) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 hover:bg-[#3A07BB] text-slate-700 hover:text-white border border-slate-200/80 hover:border-[#3A07BB] rounded-xl text-xs transition duration-200 font-normal">
                    <span>#{{ $tag->name }}</span>
                    <span class="bg-white/80 text-slate-700 text-[10px] px-1.5 py-0.2 rounded-full font-medium">{{ $tag->articles_count ?? 0 }}</span>
                </a>
            @endforeach
        </div>
    </div>
</div>
@endsection
