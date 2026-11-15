@extends('layouts.site')

@section('title', $event->title . ' - Kalender Infoscreening')
@section('description', Str::limit(strip_tags($event->description), 150))

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

    <!-- Main Detail (8 cols) -->
    <article class="lg:col-span-8 bg-white p-6 sm:p-9 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
        <nav class="text-xs text-slate-400 flex items-center gap-1.5 flex-wrap font-normal">
            <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Beranda</a>
            <i class="fas fa-angle-right text-[9px]"></i>
            <a href="{{ route('events.index') }}" class="hover:text-[#3A07BB]">Kalender</a>
            <i class="fas fa-angle-right text-[9px]"></i>
            <span class="text-slate-400 truncate max-w-xs">{{ $event->title }}</span>
        </nav>

        <div class="space-y-3">
            <div class="flex items-center gap-2">
                <span class="badge-cat text-xs">{{ $event->type }}</span>
                @if ($event->city)
                    <span class="bg-slate-800 text-white text-xs font-normal px-2.5 py-0.5 rounded-md">{{ $event->city }}</span>
                @endif
            </div>

            <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 leading-snug tracking-tight">
                {{ $event->title }}
            </h1>
        </div>

        <!-- Event Key Details Card -->
        <div class="bg-purple-50/60 border border-purple-200/80 rounded-xl p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs font-normal">
            <div class="space-y-1">
                <span class="text-slate-500 uppercase tracking-wider block text-[10px] font-medium">Waktu Mulai:</span>
                <span class="text-slate-800 font-medium text-sm flex items-center gap-1.5">
                    <i class="far fa-calendar-check text-[#3A07BB]"></i>
                    {{ $event->starts_at->translatedFormat('l, d F Y - H:i') }} WIB
                </span>
            </div>

            @if ($event->ends_at)
                <div class="space-y-1">
                    <span class="text-slate-500 uppercase tracking-wider block text-[10px] font-medium">Waktu Selesai:</span>
                    <span class="text-slate-800 font-medium text-sm flex items-center gap-1.5">
                        <i class="far fa-calendar-times text-slate-400"></i>
                        {{ $event->ends_at->translatedFormat('l, d F Y - H:i') }} WIB
                    </span>
                </div>
            @endif

            <div class="space-y-1 sm:col-span-2">
                <span class="text-slate-500 uppercase tracking-wider block text-[10px] font-medium">Lokasi / Venue:</span>
                <span class="text-slate-800 font-medium text-sm flex items-center gap-1.5">
                    <i class="fas fa-location-dot text-[#3A07BB]"></i>
                    {{ $event->venue ?? 'Online / Menyesuaikan' }} {{ $event->city ? '(' . $event->city . ')' : '' }}
                </span>
            </div>

            @if ($event->url)
                <div class="sm:col-span-2 pt-2 border-t border-purple-200/60">
                    <a href="{{ $event->url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-2 px-4 py-2 bg-[#3A07BB] hover:bg-[#2e0594] text-white text-xs font-medium rounded-lg transition shadow-xs">
                        <i class="fas fa-ticket text-xs"></i>
                        <span>Buka Tautan Resmi / Registrasi &rarr;</span>
                    </a>
                </div>
            @endif
        </div>

        @if ($event->image_url)
            <div class="overflow-hidden rounded-xl border border-slate-200/60 shadow-xs">
                <img src="{{ $event->image_url }}" alt="{{ $event->title }}" decoding="async" fetchpriority="high" class="w-full max-h-[460px] object-cover">
            </div>
        @endif

        <div class="text-slate-700 text-sm leading-relaxed space-y-3 font-normal">
            <h3 class="font-sans font-semibold text-base text-slate-800">Deskripsi Agenda</h3>
            <p>{!! nl2br(e($event->description)) !!}</p>
        </div>
    </article>

    <!-- Sidebar: Other Upcoming Events (4 cols) -->
    <aside class="lg:col-span-4 space-y-6">
        @if ($others->isNotEmpty())
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm space-y-4">
                <div class="pb-3 border-b border-slate-100">
                    <h3 class="font-sans font-semibold text-sm text-slate-800">
                        Agenda Mendatang Lainnya
                    </h3>
                </div>
                <div class="space-y-3">
                    @foreach ($others as $item)
                        <div class="border-b border-slate-100 last:border-0 pb-3 last:pb-0">
                            <span class="text-[10px] text-purple-700 font-medium uppercase">{{ $item->type }} &bull; {{ $item->city }}</span>
                            <h4 class="font-sans font-normal text-xs text-slate-800 hover:text-[#3A07BB] transition mt-0.5">
                                <a href="{{ route('events.show', ['event' => $item]) }}">{{ $item->title }}</a>
                            </h4>
                            <time class="text-[10px] text-slate-400 mt-1 block">
                                <i class="far fa-calendar-alt text-[#3A07BB] mr-1"></i> {{ $item->starts_at->translatedFormat('d M Y') }}
                            </time>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-slate-200/80 p-5 text-center space-y-2.5 shadow-sm">
            <h4 class="font-sans font-semibold text-sm text-slate-800">Punya Jadwal Pemutaran?</h4>
            <p class="text-xs text-slate-500 font-normal">Publikasikan acara komunitas atau festivalmu di Infoscreening.</p>
            <a href="{{ route('contribute.create') }}" class="inline-block w-full py-2 bg-[#3A07BB] hover:bg-[#2e0594] text-white text-xs font-medium rounded-lg transition shadow-xs">
                Kirim Agenda Pemutaran &rarr;
            </a>
        </div>
    </aside>

</div>
@endsection
