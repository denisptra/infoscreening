@extends('layouts.site')

@section('title', 'Rupa Jasa - Infoscreening')
@section('description', 'Layanan media partnership, konsultasi kurasi festival, dan publikasi perfilman oleh Infoscreening.')

@section('content')
<div class="space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <span class="badge-cat mb-2">Layanan Profesional</span>
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
            Rupa Jasa Infoscreening
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-2 max-w-3xl leading-relaxed font-normal">
            Selain sebagai media kurasi alternatif, Infoscreening menyediakan ragam layanan profesional untuk mendukung pertumbuhan ekosistem perfilman independen di Indonesia.
        </p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Service 1 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-card hover:border-[#3A07BB]/30 hover:shadow-card-hover transition duration-300 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-[#3A07BB] flex items-center justify-center text-lg">
                    <i class="fas fa-bullhorn"></i>
                </div>
                <h3 class="font-sans font-semibold text-base text-slate-900">Media Partnership &amp; Publikasi</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Dukungan publikasi terarah bagi festival film, pemutaran komunitas, peluncuran karya, dan pameran sinema melalui situs, buletin, dan jejaring sosial kami.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="text-xs font-medium text-[#3A07BB] hover:underline flex items-center gap-1.5">
                    <span>Ajukan Kemitraan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Service 2 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-card hover:border-[#3A07BB]/30 hover:shadow-card-hover transition duration-300 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-[#3A07BB] flex items-center justify-center text-lg">
                    <i class="fas fa-film"></i>
                </div>
                <h3 class="font-sans font-semibold text-base text-slate-900">Kurasi &amp; Konsultasi Program</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Pendampingan kurasi seleksi film, perancangan tema penayangan, penyusunan catatan kuratorial, serta fasilitasi diskusi pasca-pemutaran.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('directory.experts') }}" class="text-xs font-medium text-[#3A07BB] hover:underline flex items-center gap-1.5">
                    <span>Pilih Tenaga Ahli</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Service 3 -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-card hover:border-[#3A07BB]/30 hover:shadow-card-hover transition duration-300 flex flex-col justify-between">
            <div class="space-y-3">
                <div class="w-11 h-11 rounded-xl bg-purple-50 text-[#3A07BB] flex items-center justify-center text-lg">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="font-sans font-semibold text-base text-slate-900">Penyelenggaraan Pemutaran</h3>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Manajemen teknis screening alternatif, peminjaman peralatan proyeksi audio-visual, lisensi tayang, dan pengorganisasian venue komunitas.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-100">
                <a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="text-xs font-medium text-[#3A07BB] hover:underline flex items-center gap-1.5">
                    <span>Konsultasi Teknis</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
