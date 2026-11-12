@extends('layouts.site')

@section('title', 'Kalender & Agenda Sinema - Infoscreening')
@section('description', 'Jadwal pemutaran film alternatif, festival film, diskusi, dan bioskop mikro di seluruh Indonesia.')

@section('content')
<div class="space-y-8">
    <!-- Header Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
                <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Beranda</a>
                <i class="fas fa-angle-right text-[9px]"></i>
                <span class="text-[#3A07BB] font-medium">Kalender</span>
            </nav>
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-800 tracking-tight">
                Kalender Agenda Sinema
            </h1>
            <p class="text-xs text-slate-500 mt-1 font-normal max-w-xl">
                Jadwal festival, pemutaran film alternatif, bioskop mikro, dan lokakarya di berbagai kota.
            </p>
        </div>

        <a href="{{ route('contribute.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#3A07BB] hover:bg-[#2e0594] text-white text-xs font-medium rounded-xl transition shadow-sm whitespace-nowrap self-start md:self-auto">
            <i class="fas fa-plus text-xs"></i> <span>Daftarkan Agendamu</span>
        </a>
    </div>

    <!-- Filter Bar (Horizontal, Nyamping) -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm flex flex-wrap items-center justify-between gap-4 text-xs font-normal">
        <!-- Types Filter -->
        <div class="flex flex-wrap items-center gap-1.5">
            <a href="{{ route('events.index', ['waktu' => request('waktu')]) }}" class="px-3 py-1.5 rounded-lg transition {{ empty($activeType) ? 'bg-[#3A07BB] text-white font-medium' : 'text-slate-600 hover:bg-slate-100' }}">
                Semua Acara
            </a>
            @foreach ($types as $type)
                <a href="{{ route('events.index', ['jenis' => $type, 'waktu' => request('waktu')]) }}" class="px-3 py-1.5 rounded-lg transition {{ $activeType === $type ? 'bg-[#3A07BB] text-white font-medium' : 'text-slate-600 hover:bg-slate-100' }}">
                    {{ $type }}
                </a>
            @endforeach
        </div>

        <!-- Time Filter -->
        <div class="flex items-center gap-1 bg-slate-100 p-1 rounded-lg">
            <a href="{{ route('events.index', ['jenis' => $activeType]) }}" class="px-3 py-1 rounded-md transition {{ ! $showPast ? 'bg-white shadow-xs text-[#3A07BB] font-medium' : 'text-slate-500' }}">
                Mendatang
            </a>
            <a href="{{ route('events.index', ['jenis' => $activeType, 'waktu' => 'lalu']) }}" class="px-3 py-1 rounded-md transition {{ $showPast ? 'bg-white shadow-xs text-[#3A07BB] font-medium' : 'text-slate-500' }}">
                Arsip Terlaksana
            </a>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($events as $event)
            <article class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm hover:shadow-card-hover hover:border-purple-300 transition-all duration-300 flex flex-col justify-between group">
                <div>
                    <div class="relative overflow-hidden rounded-xl h-48 mb-3.5 bg-slate-100">
                        <a href="{{ route('events.show', ['event' => $event]) }}">
                            <img loading="lazy" decoding="async" src="{{ $event->image_url ?? 'https://images.unsplash.com/photo-1489599849927-2ee91cede3ba?auto=format&fit=crop&w=800&q=80' }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        </a>
                        <div class="absolute top-3 left-3 flex gap-1.5">
                            <span class="badge-cat text-[10px]">{{ $event->type }}</span>
                            @if($event->city)
                                <span class="bg-black/75 backdrop-blur-sm text-white text-[10px] font-normal px-2 py-0.5 rounded-md">{{ $event->city }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="flex items-center gap-2 text-xs text-purple-700 font-medium whitespace-nowrap">
                            <i class="far fa-calendar-alt text-[11px]"></i>
                            <span>{{ $event->starts_at->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>

                        <h2 class="font-sans font-medium text-base text-slate-800 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                            <a href="{{ route('events.show', ['event' => $event]) }}">
                                {{ $event->title }}
                            </a>
                        </h2>

                        @if ($event->venue)
                            <div class="text-xs text-slate-500 flex items-center gap-1.5 font-normal">
                                <i class="fas fa-location-dot text-[#3A07BB] text-[11px] shrink-0"></i>
                                <span class="truncate">{{ $event->venue }}</span>
                            </div>
                        @endif

                        <p class="text-xs text-slate-500 line-clamp-2 font-normal leading-relaxed">
                            {{ $event->description }}
                        </p>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('events.show', ['event' => $event]) }}" class="block text-center w-full py-2 bg-purple-50 group-hover:bg-[#3A07BB] text-[#3A07BB] group-hover:text-white text-xs font-medium rounded-lg transition">
                        Detail Agenda &rarr;
                    </a>
                </div>
            </article>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200 text-slate-400">
                <i class="far fa-calendar-times text-3xl mb-2 text-slate-300"></i>
                <p class="text-xs">Tidak ada agenda untuk filter ini.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="bg-white p-4 rounded-xl border border-slate-200/80 shadow-sm">
        {{ $events->links() }}
    </div>
</div>
@endsection
