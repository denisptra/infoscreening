@extends('layouts.site')

@section('title', $page['title'] . ' - Infoscreening')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
            <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Home</a>
            <i class="fas fa-angle-right text-[10px]"></i>
            <span class="text-[#3A07BB] font-medium">{{ $page['title'] }}</span>
        </nav>
        <span class="badge-cat mb-2">Halaman</span>
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
            {{ $page['title'] }}
        </h1>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card space-y-4 text-xs sm:text-sm text-slate-700 leading-relaxed font-normal">
        @if (is_array($page['content']))
            @foreach ($page['content'] as $paragraph)
                <p>{{ $paragraph }}</p>
            @endforeach
        @else
            <p>{{ $page['content'] }}</p>
        @endif

        @if (request()->is('kontak'))
            <div class="mt-8 pt-6 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-normal">
                <div class="bg-purple-50/60 p-4 rounded-xl border border-purple-100">
                    <span class="font-medium text-[#3A07BB] block mb-1">Email Redaksi:</span>
                    <a href="mailto:kontak@infoscreening.id" class="text-slate-800 text-sm hover:underline">kontak@infoscreening.id</a>
                </div>
                <div class="bg-purple-50/60 p-4 rounded-xl border border-purple-100">
                    <span class="font-medium text-[#3A07BB] block mb-1">Media Sosial:</span>
                    <span class="text-slate-800 text-sm">@infoscreening (X / Twitter &amp; Instagram)</span>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
