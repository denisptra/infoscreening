@extends('layouts.site')

@section('title', 'Tenaga Ahli Sinema - Infoscreening')
@section('description', 'Direktori kurator, pengarsip, programmer festival, dan praktisi sinema profesional mitra Infoscreening.')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <nav class="text-xs text-slate-400 mb-2 flex items-center gap-1.5 font-normal">
                <a href="{{ route('home') }}" class="hover:text-[#3A07BB]">Home</a>
                <i class="fas fa-angle-right text-[10px]"></i>
                <a href="{{ route('directory.services') }}" class="hover:text-[#3A07BB]">Rupa Jasa</a>
                <i class="fas fa-angle-right text-[10px]"></i>
                <span class="text-[#3A07BB] font-medium">Tenaga Ahli</span>
            </nav>
            <span class="badge-cat mb-2">Direktori Profesional</span>
            <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
                Tenaga Ahli Perfilman
            </h1>
            <p class="text-xs sm:text-sm text-slate-600 mt-1 max-w-2xl leading-relaxed font-normal">
                Jejaring praktisi, kurator, dan konsultan perfilman yang siap berkolaborasi untuk proyek sinema, lokakarya, atau festivalmu.
            </p>
        </div>

        <a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#3A07BB] text-white font-medium text-xs rounded-xl hover:bg-[#2e0594] transition self-start md:self-auto shadow-sm whitespace-nowrap">
            <i class="fas fa-handshake text-xs"></i>
            <span>Konsultasi Kerjasama</span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @forelse ($experts as $expert)
            <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-card hover:border-[#3A07BB]/30 hover:shadow-card-hover transition duration-300 flex flex-col sm:flex-row gap-5">
                <div class="shrink-0">
                    <img loading="lazy" decoding="async"
                        src="{{ $expert->photo_url ?? 'https://images.unsplash.com/photo-1534528741775-53994a69daeb?auto=format&fit=crop&w=400&q=80' }}"
                        alt="{{ $expert->name }}"
                        class="w-20 h-20 sm:w-24 sm:h-24 rounded-full object-cover border-2 border-purple-100"
                    >
                </div>
                <div class="space-y-2 flex-1">
                    <div>
                        <span class="text-[10px] font-medium text-[#3A07BB] bg-purple-50 px-2 py-0.5 rounded-md inline-block">
                            {{ $expert->city ?? 'Indonesia' }}
                        </span>
                        <h3 class="font-sans font-semibold text-base text-slate-900 mt-1">{{ $expert->name }}</h3>
                        <p class="text-xs font-normal text-[#3A07BB]">{{ $expert->specialty }}</p>
                    </div>

                    <p class="text-xs text-slate-600 leading-relaxed font-normal">
                        {{ $expert->bio }}
                    </p>

                    @if ($expert->email)
                        <div class="pt-2 text-xs text-slate-500 flex items-center gap-2 font-normal">
                            <i class="far fa-envelope text-[#3A07BB]"></i>
                            <a href="mailto:{{ $expert->email }}" class="hover:underline text-slate-700">{{ $expert->email }}</a>
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white p-12 text-center rounded-2xl border border-slate-200/80">
                <p class="text-xs text-slate-500 font-normal">Belum ada profil tenaga ahli yang terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
