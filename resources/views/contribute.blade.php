@extends('layouts.site')

@section('title', 'Berkontribusi - Infoscreening')
@section('description', 'Kirimkan artikel, ulasan, agenda festival, foto, atau liputan pemutaran film alternatifmu ke Infoscreening.')

@section('content')
<div class="max-w-4xl mx-auto space-y-8">
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <span class="badge-cat mb-2">Partisipasi Publik</span>
        <h1 class="font-sans font-semibold text-2xl sm:text-3xl text-slate-900 tracking-tight">
            Berkontribusi di Infoscreening
        </h1>
        <p class="text-xs sm:text-sm text-slate-600 mt-2 leading-relaxed font-normal">
            Infoscreening lahir dari semangat gotong royong komunitas sinema nusantara. Kami membuka pintu selebar-lebarnya bagi Anda yang ingin membagikan kabar pemutaran film, ulasan kritis, liputan festival daerah, maupun riset perfilman.
        </p>
    </div>

    <!-- Submission Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-2xl border border-slate-200/80 shadow-card">
        <form method="POST" action="{{ route('contribute.store') }}" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Nama -->
                <div>
                    <label for="name" class="block text-xs font-medium text-slate-700 mb-1.5">
                        Nama Lengkap / Komunitas <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="name"
                        id="name"
                        value="{{ old('name') }}"
                        required
                        class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
                        placeholder="Contoh: Rian Ardiansyah (Klub Film)"
                    >
                    @error('name')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-xs font-medium text-slate-700 mb-1.5">
                        Alamat Surel / Email <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
                        placeholder="nama@domain.com"
                    >
                    @error('email')
                        <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Jenis Kontribusi -->
            <div>
                <label for="type" class="block text-xs font-medium text-slate-700 mb-1.5">
                    Jenis Kontribusi <span class="text-red-500">*</span>
                </label>
                <select
                    name="type"
                    id="type"
                    required
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
                >
                    @foreach ($types as $type)
                        <option value="{{ $type }}" {{ old('type') === $type ? 'selected' : '' }}>
                            {{ $type }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Judul -->
            <div>
                <label for="title" class="block text-xs font-medium text-slate-700 mb-1.5">
                    Judul Tulisan / Nama Agenda <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="title"
                    id="title"
                    value="{{ old('title') }}"
                    required
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
                    placeholder="Contoh: Mengurai Realitas Sineas Perempuan dalam Dokumenter Terkini"
                >
                @error('title')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tautan Pendukung (Google Drive / Doc / Poster) -->
            <div>
                <label for="link" class="block text-xs font-medium text-slate-700 mb-1.5">
                    Tautan Dokumen / Poster / Google Drive (Opsional)
                </label>
                <input
                    type="url"
                    name="link"
                    id="link"
                    value="{{ old('link') }}"
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
                    placeholder="https://drive.google.com/..."
                >
                <p class="text-[11px] text-slate-500 mt-1 font-normal">Sertakan tautan draft Google Docs, foto beresolusi tinggi, atau siaran pers (pastikan akses publik dibuka).</p>
                @error('link')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Pesan / Naskah Ringkas -->
            <div>
                <label for="message" class="block text-xs font-medium text-slate-700 mb-1.5">
                    Pesan Pengantar / Naskah / Detail Agenda <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="message"
                    id="message"
                    rows="6"
                    required
                    class="w-full border border-slate-300 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-1 focus:ring-[#3A07BB] focus:border-[#3A07BB]"
                    placeholder="Tuliskan naskah lengkap atau ringkasan kegiatan dan kontak yang dapat dihubungi..."
                >{{ old('message') }}</textarea>
                @error('message')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button
                type="submit"
                class="w-full sm:w-auto px-6 py-2.5 bg-[#3A07BB] text-white font-medium text-xs rounded-xl hover:bg-[#2e0594] transition shadow-sm flex items-center justify-center gap-2"
            >
                <i class="fas fa-paper-plane text-xs"></i>
                <span>Kirimkan ke Redaksi</span>
            </button>
        </form>
    </div>
</div>
@endsection
