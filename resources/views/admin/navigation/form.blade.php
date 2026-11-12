@extends('layouts.admin')

@section('page_title', $navigation->exists ? 'Edit Menu' : 'Tambah Menu')

@section('content')
<div class="mx-auto max-w-3xl space-y-6" x-data="{
    onPresetChange(event) {
        const val = event.target.value;
        if (!val) return;
        const parts = val.split('|');
        const url = parts[0];
        const label = parts[1] || '';
        
        const urlInput = document.getElementById('url');
        const labelInput = document.getElementById('label');
        
        if (urlInput) {
            urlInput.value = url;
            urlInput.dispatchEvent(new Event('input'));
        }
        if (labelInput && (!labelInput.value || labelInput.value.trim() === '')) {
            labelInput.value = label;
            labelInput.dispatchEvent(new Event('input'));
        }
    }
}">
    <div>
        <a href="{{ route('strapi.navigation.index') }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1.5">
            <i class="fas fa-arrow-left text-[10px]"></i>
            Kembali ke Navigasi &amp; Footer
        </a>
        <h1 class="mt-2 text-xl font-semibold text-[#32324d]">{{ $navigation->exists ? 'Edit Menu' : 'Tambah Menu' }}</h1>
        <p class="mt-1 text-xs text-[#666687]">Atur struktur menu header portal atau footer website. Submenu dropdown dibatasi satu tingkat.</p>
    </div>

    <form method="POST" action="{{ $navigation->exists ? route('strapi.navigation.update', $navigation) : route('strapi.navigation.store') }}" class="space-y-5 rounded-xl border border-[#eaeaef] bg-white p-5 shadow-sm">
        @csrf
        @if ($navigation->exists)
            @method('PUT')
        @endif

        <div>
            <label for="label" class="mb-1.5 block text-xs font-medium text-[#32324d]">Nama Menu (Label)</label>
            <input id="label" name="label" value="{{ old('label', $navigation->label) }}" required maxlength="100" placeholder="Misal: Berita Film, Festival, Kalender Event" class="w-full rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]">
            @error('label')
                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            <div>
                <label for="location" class="mb-1.5 block text-xs font-medium text-[#32324d]">Tampilkan di Area</label>
                <select id="location" name="location" required class="w-full rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d]">
                    <option value="header" @selected(old('location', $navigation->location) === 'header')>Header (Navbar Atas)</option>
                    <option value="footer" @selected(old('location', $navigation->location) === 'footer')>Footer (Bawah Halaman)</option>
                </select>
                @error('location')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="parent_id" class="mb-1.5 block text-xs font-medium text-[#32324d]">Menu Induk (Opsional)</label>
                <select id="parent_id" name="parent_id" class="w-full rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d]">
                    <option value="">Menu Utama (Tampil langsung di navbar)</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}" @selected((string) old('parent_id', $navigation->parent_id) === (string) $parent->id)>
                            {{ $parent->location === 'header' ? 'Header' : 'Footer' }} — {{ $parent->label }}
                        </option>
                    @endforeach
                </select>
                <p class="mt-1 text-[10px] text-[#8e8ea9]">Pilih menu induk jika ingin menjadikannya dropdown.</p>
                @error('parent_id')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Quick Preset Picker Dropdown -->
        <div class="rounded-lg bg-[#f0f0ff] border border-[#d9d8ff] p-3.5">
            <div class="flex items-center justify-between mb-1.5">
                <label for="quick_preset" class="text-xs font-semibold text-[#3A07BB] flex items-center gap-1.5">
                    <i class="fas fa-wand-magic-sparkles text-[#4945ff]"></i>
                    Pilih Tautan Cepat / Kategori (Otomatis Isi)
                </label>
                <span class="text-[10px] text-[#666687]">Klik untuk memilih rute standar</span>
            </div>
            <select id="quick_preset" @change="onPresetChange($event)" class="w-full rounded-md border border-[#c4c4f0] bg-white px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]">
                <option value="">-- Pilih Halaman Utama atau Kategori Rubrik --</option>
                <optgroup label="Halaman Utama Portal">
                    <option value="/|Beranda">Beranda (/)</option>
                    <option value="/artikel|Semua Artikel">Semua Artikel (/artikel)</option>
                    <option value="/kategori|Daftar Kategori">Daftar Kategori Rubrik (/kategori)</option>
                    <option value="/events|Kalender Event">Kalender Event & Acara (/events)</option>
                    <option value="/berkontribusi|Berkontribusi">Kirim Tulisan / Berkontribusi (/berkontribusi)</option>
                    <option value="/rupa-jasa|Rupa Jasa">Layanan Rupa Jasa (/rupa-jasa)</option>
                    <option value="/rupa-jasa/tenaga-ahli|Tenaga Ahli Sinema">Tenaga Ahli Sinema (/rupa-jasa/tenaga-ahli)</option>
                    <option value="/toko|Toko / Zine">Toko / Zine (/toko)</option>
                    <option value="/tentang-kami|Tentang Kami">Tentang Kami (/tentang-kami)</option>
                    <option value="/authors-list|Daftar Penulis">Daftar Penulis (/authors-list)</option>
                    <option value="/kontak|Kontak Redaksi">Kontak Redaksi (/kontak)</option>
                    <option value="/kebijakan-privasi|Kebijakan Privasi">Kebijakan Privasi (/kebijakan-privasi)</option>
                </optgroup>
                @if(isset($categories) && $categories->isNotEmpty())
                    <optgroup label="Rubrik / Kategori Tulisan (/kategori/{slug})">
                        @foreach($categories as $cat)
                            <option value="/kategori/{{ $cat->slug }}|{{ $cat->name }}">{{ $cat->name }} (/kategori/{{ $cat->slug }})</option>
                        @endforeach
                    </optgroup>
                @endif
            </select>
        </div>

        <div>
            <label for="url" class="mb-1.5 block text-xs font-medium text-[#32324d]">Alamat Tautan (URL)</label>
            <input id="url" name="url" value="{{ old('url', $navigation->url) }}" maxlength="2048" placeholder="/artikel, /kategori/berita-film, atau https://..." class="w-full rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d] font-mono focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]">
            @error('url')
                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror

            <!-- Info Guide Box -->
            <div class="mt-2.5 rounded-lg border border-[#eaeaef] bg-[#f8f9fc] p-3 text-[11px] text-[#666687] space-y-1">
                <p class="font-semibold text-[#32324d] flex items-center gap-1.5">
                    <i class="fas fa-circle-info text-[#4945ff]"></i>
                    Panduan Struktur URL:
                </p>
                <ul class="list-disc list-inside space-y-1 pl-1">
                    <li><strong class="text-[#32324d]">Rubrik Kategori:</strong> Gunakan selalu format <code class="rounded bg-white px-1.5 py-0.5 border border-[#dcdce4] font-semibold text-[#3A07BB]">/kategori/{slug}</code> (misal: <code>/kategori/berita-film</code>). Format lama <code>/category/</code> otomatis diubah ke <code>/kategori/</code> oleh sistem.</li>
                    <li><strong class="text-[#32324d]">Semua Artikel:</strong> Gunakan <code class="rounded bg-white px-1.5 py-0.5 border border-[#dcdce4] font-semibold text-[#3A07BB]">/artikel</code> untuk menampilkan indeks seluruh artikel.</li>
                    <li><strong class="text-[#32324d]">Menu Induk Dropdown:</strong> Jika menu ini berfungsi sebagai tombol dropdown di header (seperti tombol "Artikel"), Anda dapat mengisinya dengan <code class="rounded bg-white px-1.5 py-0.5 border border-[#dcdce4] font-semibold text-[#3A07BB]">/artikel</code> atau mengosongkannya.</li>
                </ul>
            </div>
        </div>

        <div>
            <label for="sort_order" class="mb-1.5 block text-xs font-medium text-[#32324d]">Urutan Tampil (Angka Kecil di Depan)</label>
            <input id="sort_order" name="sort_order" type="number" min="0" max="1000" value="{{ old('sort_order', $navigation->sort_order ?? 10) }}" required class="w-32 rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d]">
            @error('sort_order')
                <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-wrap gap-5 pt-1">
            <label class="inline-flex items-center gap-2 text-xs text-[#32324d] cursor-pointer">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $navigation->is_active ?? true)) class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]">
                <span>Aktifkan menu ini (terlihat oleh pengunjung)</span>
            </label>
            <label class="inline-flex items-center gap-2 text-xs text-[#32324d] cursor-pointer">
                <input type="checkbox" name="open_in_new_tab" value="1" @checked(old('open_in_new_tab', $navigation->open_in_new_tab)) class="rounded border-[#dcdce4] text-[#4945ff] focus:ring-[#4945ff]">
                <span>Buka tautan di tab baru (target="_blank")</span>
            </label>
        </div>

        <div class="pt-2 flex items-center gap-3">
            <button type="submit" class="rounded-lg bg-[#4945ff] px-5 py-2.5 text-xs font-semibold text-white hover:bg-[#3b37eb] transition">
                <i class="fas fa-check mr-1.5"></i>
                Simpan Menu
            </button>
            <a href="{{ route('strapi.navigation.index') }}" class="rounded-lg border border-[#dcdce4] bg-white px-4 py-2.5 text-xs font-medium text-[#666687] hover:bg-slate-50 transition">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection
