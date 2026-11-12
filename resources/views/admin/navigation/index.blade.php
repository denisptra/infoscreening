@extends('layouts.admin')

@section('page_title', 'Navigasi & Footer')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="text-xl font-semibold text-[#32324d]">Navigasi &amp; Footer</h1>
            <p class="mt-1 max-w-2xl text-xs leading-relaxed text-[#666687]">
                Kelola menu navigasi navbar, dropdown artikel/rubrik, dan tautan footer portal. Seluruh tautan menggunakan standar rute Indonesia (<code class="bg-[#f0f0ff] px-1.5 py-0.5 rounded text-[#3A07BB]">/kategori/...</code> dan <code class="bg-[#f0f0ff] px-1.5 py-0.5 rounded text-[#3A07BB]">/artikel</code>).
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5">
            <form method="POST" action="{{ route('strapi.navigation.normalize-urls') }}" data-confirm data-confirm-title="Normalkan Semua URL Menu?" data-confirm-message="Semua URL menu lama yang masih mengandung /category/ akan otomatis diubah ke /kategori/." data-confirm-action="Normalkan URL">
                @csrf
                <button type="submit" class="inline-flex items-center gap-1.5 rounded-lg border border-[#dcdce4] bg-white px-3.5 py-2 text-xs font-medium text-[#32324d] hover:bg-slate-50 transition" title="Perbaiki semua link lama /category/ menjadi /kategori/">
                    <i class="fas fa-wand-magic-sparkles text-[#4945ff]"></i>
                    Normalkan URL (/category/ &rarr; /kategori/)
                </button>
            </form>

            <a href="{{ route('strapi.navigation.create') }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#4945ff] px-4 py-2 text-xs font-medium text-white hover:bg-[#3b37eb] transition shadow-sm">
                <i class="fas fa-plus text-[10px]"></i>
                Tambah Menu
            </a>
        </div>
    </div>

    <!-- Quick Navigation Hierarchy Explanation -->
    <div class="rounded-xl border border-[#d9d8ff] bg-[#f8f9ff] p-4 text-xs text-[#4945ff]">
        <div class="flex items-start gap-3">
            <i class="fas fa-circle-question text-base mt-0.5 text-[#4945ff]"></i>
            <div class="space-y-1">
                <span class="font-semibold text-[#3A07BB]">Struktur Dropdown & Standar URL Infoscreening:</span>
                <p class="text-[11px] leading-relaxed text-[#5552a0]">
                    &bull; <strong>Dropdown Menu:</strong> Menu utama seperti <strong>Artikel</strong> bertindak sebagai induk. Submenu di bawahnya diarahkan ke masing-masing rubrik (misal: <code>/kategori/berita-film</code>, <code>/kategori/festival-film</code>).<br>
                    &bull; <strong>Perbedaan /artikel vs /kategori/{slug}:</strong> <code>/artikel</code> menampilkan <em>seluruh</em> arsip tulisan, sedangkan <code>/kategori/nama-rubrik</code> memfilter tulisan khusus rubrik tersebut.<br>
                    &bull; <strong>Redirect Otomatis:</strong> Akses pengunjung ke link lawas <code>/category/...</code> kini secara otomatis dialihkan (301 Permanent Redirect) ke <code>/kategori/...</code> tanpa error.
                </p>
            </div>
        </div>
    </div>

    @foreach (['header' => 'Menu Header (Navbar Utama)', 'footer' => 'Menu Footer (Bawah Situs)'] as $location => $heading)
        <section class="overflow-hidden rounded-xl border border-[#eaeaef] bg-white shadow-sm">
            <div class="border-b border-[#eaeaef] px-5 py-4 bg-slate-50/50 flex items-center justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-[#32324d]">{{ $heading }}</h2>
                    <p class="mt-0.5 text-[11px] text-[#8e8ea9]">
                        @if ($location === 'header')
                            Tampil di bagian navbar paling atas. Menu dengan submenu otomatis tampil sebagai dropdown.
                        @else
                            Tampil di bagian footer portal dalam beberapa kolom rubrikasi dan informasi.
                        @endif
                    </p>
                </div>
            </div>
            <div class="divide-y divide-[#eaeaef]">
                @forelse ($itemsByLocation->get($location, collect()) as $item)
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-start sm:justify-between hover:bg-slate-50/40 transition">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-xs font-semibold text-[#32324d] flex items-center gap-1.5">
                                    @if ($item->children->isNotEmpty())
                                        <i class="fas fa-folder-tree text-[#8e8ea9] text-[11px]"></i>
                                    @else
                                        <i class="fas fa-link text-[#8e8ea9] text-[11px]"></i>
                                    @endif
                                    {{ $item->label }}
                                </span>
                                <span class="rounded bg-slate-100 px-1.5 py-0.5 text-[10px] font-mono text-slate-500">Urutan: {{ $item->sort_order }}</span>
                                @if (! $item->is_active)
                                    <span class="rounded bg-amber-50 px-2 py-0.5 text-[10px] text-amber-700">Nonaktif</span>
                                @endif
                                @if ($item->open_in_new_tab)
                                    <span class="rounded bg-blue-50 px-1.5 py-0.5 text-[10px] text-blue-700">Tab Baru</span>
                                @endif
                            </div>
                            @if ($item->url)
                                <p class="mt-1 break-all text-[11px] font-mono text-[#8e8ea9] flex items-center gap-1">
                                    <span class="text-[#3A07BB]">{{ $item->url }}</span>
                                </p>
                            @else
                                <p class="mt-1 text-[11px] italic text-[#8e8ea9]">(Wadah dropdown tanpa tautan)</p>
                            @endif

                            @if ($item->children->isNotEmpty())
                                <ul class="mt-3 space-y-2 border-l-2 border-[#4945ff]/20 pl-3.5">
                                    @foreach ($item->children as $child)
                                        <li class="flex flex-wrap items-center justify-between gap-x-2 gap-y-1 text-[11px] bg-slate-50 p-2 rounded-lg border border-slate-100">
                                            <div class="flex items-center gap-2">
                                                <i class="fas fa-arrow-turn-down text-[10px] text-[#4945ff]"></i>
                                                <span class="font-medium text-[#32324d]">{{ $child->label }}</span>
                                                <span class="break-all font-mono text-[10px] text-[#3A07BB]">{{ $child->url }}</span>
                                                <span class="text-[9px] text-slate-400 font-mono">({{ $child->sort_order }})</span>
                                                @if (! $child->is_active)
                                                    <span class="text-amber-700 font-medium">Nonaktif</span>
                                                @endif
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('strapi.navigation.edit', $child) }}" class="text-[11px] text-[#4945ff] hover:underline font-medium">Edit</a>
                                                <span class="text-slate-300">|</span>
                                                <form method="POST" action="{{ route('strapi.navigation.destroy', $child) }}" data-confirm data-confirm-title="Hapus submenu?" data-confirm-message="Submenu ini akan dihapus permanen." data-confirm-action="Hapus submenu">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-[11px] text-red-600 hover:underline">Hapus</button>
                                                </form>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <a href="{{ route('strapi.navigation.edit', $item) }}" class="text-xs font-medium text-[#4945ff] hover:underline flex items-center gap-1">
                                <i class="fas fa-pen-to-square text-[10px]"></i>
                                Edit
                            </a>
                            <span class="text-slate-300">|</span>
                            <form method="POST" action="{{ route('strapi.navigation.destroy', $item) }}" data-confirm data-confirm-title="Hapus menu?" data-confirm-message="Menu ini beserta seluruh submenu di bawahnya akan dihapus permanen." data-confirm-action="Hapus menu">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs font-medium text-red-600 hover:underline flex items-center gap-1">
                                    <i class="fas fa-trash text-[10px]"></i>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="p-5 text-xs text-[#8e8ea9]">Belum ada menu untuk area ini.</p>
                @endforelse
            </div>
        </section>
    @endforeach

    <section class="rounded-xl border border-[#eaeaef] bg-white p-5 shadow-sm">
        <div class="mb-5">
            <h2 class="text-sm font-semibold text-[#32324d]">Teks Footer Portal</h2>
            <p class="mt-1 text-[11px] text-[#8e8ea9]">Atur keterangan profil singkat dan teks hak cipta yang tampil di bagian paling bawah website.</p>
        </div>
        <form method="POST" action="{{ route('strapi.appearance.footer.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="footer_about" class="mb-1.5 block text-xs font-medium text-[#32324d]">Keterangan Singkat Profil</label>
                <textarea id="footer_about" name="footer_about" rows="3" required maxlength="1000" class="w-full rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]">{{ old('footer_about', $footerAbout) }}</textarea>
                @error('footer_about')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label for="footer_copyright" class="mb-1.5 block text-xs font-medium text-[#32324d]">Teks Hak Cipta (Copyright)</label>
                <input id="footer_copyright" name="footer_copyright" value="{{ old('footer_copyright', $footerCopyright) }}" required maxlength="150" class="w-full rounded-md border border-[#dcdce4] px-3 py-2 text-xs text-[#32324d] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff]">
                @error('footer_copyright')
                    <p class="mt-1 text-[11px] text-red-600">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="rounded-lg bg-[#4945ff] px-4 py-2 text-xs font-medium text-white hover:bg-[#3b37eb] transition">
                <i class="fas fa-check mr-1"></i>
                Simpan Footer
            </button>
        </form>
    </section>
</div>
@endsection
