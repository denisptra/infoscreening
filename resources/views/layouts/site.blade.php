<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Infoscreening - Festival Film dan Rupa-Rupa Sinema')</title>
    <meta name="description" content="@yield('description', 'Sinema Alternatif dan Banyak Lagi. Informasi pemutaran alternatif, festival film, dan ekosistem sinema Indonesia.')">

    <!-- Tab Favicon (Emblem) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Self-hosted font preload (avoids render-blocking Google Fonts request) -->
    <link
        rel="preload"
        as="font"
        type="font/woff2"
        href="{{ asset('fonts/filament/filament/inter/inter-latin-wght-normal-DIHYUR35.woff2') }}"
        crossorigin
    >

    <!-- Font Awesome (non-blocking, with no-JS fallback) -->
    <link rel="preconnect" href="https://cdnjs.cloudflare.com" crossorigin>
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        media="print"
        onload="this.media='all'"
    >
    <noscript>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    </noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#f8fafc] text-slate-800 min-h-screen flex flex-col font-sans antialiased selection:bg-[#3A07BB] selection:text-white" x-data="{ mobileMenu: false }">

    <!-- Top Info Bar (Translucent & Minimal) -->
    <div class="bg-[#190457]/90 backdrop-blur-md text-slate-300 text-xs border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5 flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex items-center gap-3 text-[11px] text-slate-300 whitespace-nowrap">
                <span class="flex items-center gap-1.5 text-purple-200">
                    <i class="far fa-calendar-alt text-xs"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </span>
                <span class="text-white/20 hidden sm:inline">|</span>
                <span class="text-slate-300/80 hidden sm:inline font-normal">Sinema Alternatif &amp; Festival Film Indonesia</span>
            </div>

            <div class="flex items-center gap-3.5 text-xs whitespace-nowrap">
                <div class="flex items-center gap-2.5 text-slate-300">
                    <a href="https://facebook.com" target="_blank" rel="noopener" class="hover:text-white transition" title="Facebook"><i class="fab fa-facebook-f text-[11px]"></i></a>
                    <a href="https://twitter.com/infoscreening" target="_blank" rel="noopener" class="hover:text-white transition" title="Twitter / X"><i class="fab fa-x-twitter text-[11px]"></i></a>
                    <a href="https://instagram.com/infoscreening" target="_blank" rel="noopener" class="hover:text-white transition" title="Instagram"><i class="fab fa-instagram text-[11px]"></i></a>
                    <a href="https://youtube.com/user/infoscreening" target="_blank" rel="noopener" class="hover:text-white transition" title="YouTube"><i class="fab fa-youtube text-[11px]"></i></a>
                </div>
                <span class="text-white/20">|</span>
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ url('/admin') }}" class="inline-flex items-center gap-1.5 font-medium text-amber-300 hover:text-white transition text-xs">
                            <i class="fas fa-gauge text-[11px]"></i> <span>Panel Admin</span>
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="hover:text-white font-medium transition text-xs">
                            <i class="fas fa-user-circle mr-1"></i> Dashboard
                        </a>
                    @endif
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-slate-400 hover:text-red-300 transition text-[11px] ml-1">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="hover:text-white font-medium transition text-xs flex items-center gap-1">
                        <i class="far fa-user text-[11px]"></i> <span>Masuk</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Navigation Bar (#3A07BB with Background Transparency & Backdrop Blur) -->
    <header class="bg-[#3A07BB]/95 backdrop-blur-md sticky top-0 z-50 shadow-sm border-b border-white/10 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 gap-4">

                <!-- Left: Hamburger (Mobile) + Enhanced Transparent Logo -->
                <div class="flex items-center gap-3 shrink-0">
                    <button @click="mobileMenu = !mobileMenu" class="lg:hidden p-2 text-white/90 hover:text-white hover:bg-white/10 rounded-lg transition focus:outline-none" aria-label="Menu">
                        <i class="fas" :class="mobileMenu ? 'fa-xmark text-lg' : 'fa-bars text-lg'"></i>
                    </button>

                    <a href="{{ route('home') }}" class="flex items-center gap-2 group py-1">
                        <!-- Responsive Transparent Logo -->
                        <x-application-logo variant="white" class="h-6 sm:h-7 md:h-8 w-auto max-w-[190px] sm:max-w-none group-hover:opacity-90" />
                    </a>
                </div>

                <!-- Center: Desktop Horizontal Menu (Text-only hover, no box button, Inter font) -->
                <nav class="hidden lg:flex items-center space-x-2 text-[13px] font-normal text-white/80 whitespace-nowrap">
                    <a
                        href="{{ route('home') }}"
                        class="py-2 px-2.5 transition duration-200 hover:text-white {{ request()->routeIs('home') ? 'text-white font-medium' : 'text-white/80' }}"
                    >
                        <span>Beranda</span>
                    </a>

                    <!-- Artikel Dropdown -->
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a
                            href="{{ route('articles.index') }}"
                            class="py-2 px-2.5 transition duration-200 hover:text-white flex items-center gap-1.5 {{ request()->routeIs('articles.*') ? 'text-white font-medium' : 'text-white/80' }}"
                        >
                            <span>Artikel</span>
                            <i class="fas fa-chevron-down text-[9px] opacity-70 group-hover:rotate-180 transition-transform duration-200"></i>
                        </a>
                        <div x-show="open" x-cloak class="absolute left-0 top-full pt-2 w-48 z-50">
                            <div class="bg-white/95 backdrop-blur-md text-slate-700 shadow-xl border border-slate-100 rounded-xl py-2 overflow-hidden text-xs">
                                <a href="{{ route('categories.show', ['category' => 'berita']) }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Berita Film</a>
                                <a href="{{ route('categories.show', ['category' => 'wawancara']) }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Wawancara Sineas</a>
                                <a href="{{ route('categories.show', ['category' => 'kritik-film']) }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Kritik &amp; Esai</a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <a href="{{ route('articles.index') }}" class="block px-4 py-2 font-medium text-[#3A07BB] hover:text-[#280482] hover:bg-slate-50/70 transition">Semua Artikel &rarr;</a>
                            </div>
                        </div>
                    </div>

                    <a
                        href="{{ route('events.index') }}"
                        class="py-2 px-2.5 transition duration-200 hover:text-white {{ request()->routeIs('events.*') || request()->routeIs('calendar.*') ? 'text-white font-medium' : 'text-white/80' }}"
                    >
                        <span>Kalender Event</span>
                    </a>

                    <a
                        href="{{ route('contribute.create') }}"
                        class="py-2 px-2.5 transition duration-200 hover:text-white {{ request()->routeIs('contribute.*') ? 'text-white font-medium' : 'text-white/80' }}"
                    >
                        <span>Berkontribusi</span>
                    </a>

                    <!-- Tentang Kami Dropdown -->
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a
                            href="{{ route('pages.show', ['slug' => 'tentang-kami']) }}"
                            class="py-2 px-2.5 transition duration-200 hover:text-white flex items-center gap-1.5 {{ request()->is('tentang-kami') || request()->routeIs('directory.authors') ? 'text-white font-medium' : 'text-white/80' }}"
                        >
                            <span>Tentang Kami</span>
                            <i class="fas fa-chevron-down text-[9px] opacity-70 group-hover:rotate-180 transition-transform duration-200"></i>
                        </a>
                        <div x-show="open" x-cloak class="absolute left-0 top-full pt-2 w-44 z-50">
                            <div class="bg-white/95 backdrop-blur-md text-slate-700 shadow-xl border border-slate-100 rounded-xl py-2 overflow-hidden text-xs">
                                <a href="{{ route('pages.show', ['slug' => 'tentang-kami']) }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Profil Infoscreening</a>
                                <a href="{{ route('directory.authors') }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Authors List</a>
                                <a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Kontak Redaksi</a>
                                <a href="{{ route('pages.show', ['slug' => 'kebijakan-privasi']) }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Kebijakan Privasi</a>
                            </div>
                        </div>
                    </div>

                    <!-- Rupa Jasa Dropdown -->
                    <div class="relative group" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <a
                            href="{{ route('directory.services') }}"
                            class="py-2 px-2.5 transition duration-200 hover:text-white flex items-center gap-1.5 {{ request()->routeIs('directory.services') || request()->routeIs('directory.experts') ? 'text-white font-medium' : 'text-white/80' }}"
                        >
                            <span>Rupa Jasa</span>
                            <i class="fas fa-chevron-down text-[9px] opacity-70 group-hover:rotate-180 transition-transform duration-200"></i>
                        </a>
                        <div x-show="open" x-cloak class="absolute left-0 top-full pt-2 w-48 z-50">
                            <div class="bg-white/95 backdrop-blur-md text-slate-700 shadow-xl border border-slate-100 rounded-xl py-2 overflow-hidden text-xs">
                                <a href="{{ route('directory.services') }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Layanan Kemitraan</a>
                                <a href="{{ route('directory.experts') }}" class="block px-4 py-2 text-slate-600 hover:text-[#3A07BB] hover:bg-slate-50/70 transition">Tenaga Ahli Sinema</a>
                            </div>
                        </div>
                    </div>

                    <a
                        href="{{ route('directory.shop') }}"
                        class="py-2 px-2.5 transition duration-200 hover:text-white {{ request()->routeIs('directory.shop') ? 'text-white font-medium' : 'text-white/80' }}"
                    >
                        <span>Toko / Zine</span>
                    </a>
                </nav>

                <!-- Right: Clean Search Bar (Responsive) -->
                <div class="flex items-center gap-2 shrink-0">
                    <form method="GET" action="{{ route('search.index') }}" class="relative flex items-center">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari artikel..."
                            class="text-xs rounded-full bg-white/10 hover:bg-white/15 focus:bg-white text-white focus:text-slate-800 placeholder-white/60 focus:placeholder-slate-400 border border-white/20 pl-3.5 pr-8 py-1.5 focus:outline-none focus:ring-2 focus:ring-white w-28 sm:w-44 md:w-56 transition-all duration-300 backdrop-blur-sm"
                        >
                        <button type="submit" class="absolute right-2.5 text-white/70 hover:text-white focus:outline-none" title="Cari">
                            <i class="fas fa-search text-xs"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Mobile Drawer Menu with Background Transparency & Glassmorphism -->
        <div
            x-show="mobileMenu"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="lg:hidden bg-[#24047a]/95 backdrop-blur-xl border-t border-white/10 text-white px-5 py-4 space-y-2 text-sm font-normal shadow-2xl"
        >
            <div class="pb-2 border-b border-white/10 flex items-center justify-between">
                <x-application-logo variant="white" class="h-6 w-auto" />
                <span class="text-[11px] text-purple-200 font-normal">Sinema Alternatif</span>
            </div>
            <a href="{{ route('home') }}" class="block py-2 text-white/90 hover:text-white transition">Beranda</a>
            <a href="{{ route('articles.index') }}" class="block py-2 text-white/90 hover:text-white transition">Artikel &amp; Resensi</a>
            <a href="{{ route('events.index') }}" class="block py-2 text-white/90 hover:text-white transition">Kalender Event Sinema</a>
            <a href="{{ route('contribute.create') }}" class="block py-2 text-white/90 hover:text-white transition">Berkontribusi</a>
            <a href="{{ route('directory.experts') }}" class="block py-2 text-white/90 hover:text-white transition">Tenaga Ahli (Rupa Jasa)</a>
            <a href="{{ route('directory.shop') }}" class="block py-2 text-white/90 hover:text-white transition">Toko Merchandise &amp; Zine</a>
            <a href="{{ route('pages.show', ['slug' => 'tentang-kami']) }}" class="block py-2 text-white/90 hover:text-white transition">Tentang Kami</a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if (session('status'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-800 p-4 rounded-xl shadow-sm flex items-center justify-between text-xs font-medium">
                <div class="flex items-center gap-2">
                    <i class="fas fa-check-circle text-emerald-600"></i>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Refined Footer -->
    <footer class="bg-[#181824] text-slate-300 mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Col 1: Logo & About -->
                <div class="space-y-4">
                    <a href="{{ route('home') }}" class="inline-block">
                        <x-application-logo variant="white" class="h-7 w-auto opacity-95" />
                    </a>
                    <p class="text-xs text-slate-400 leading-relaxed font-normal">
                        Dimulai dari kegemaran menonton film-film pada festival film dan pemutaran alternatif, InfoScreening merupakan media informasi pemutaran sinema alternatif di Indonesia sejak tahun 2012.
                    </p>
                    <div class="flex items-center gap-2.5 pt-1 text-white">
                        <a href="https://facebook.com" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#3A07BB] flex items-center justify-center text-xs transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://twitter.com/infoscreening" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#3A07BB] flex items-center justify-center text-xs transition"><i class="fab fa-x-twitter"></i></a>
                        <a href="https://instagram.com/infoscreening" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#3A07BB] flex items-center justify-center text-xs transition"><i class="fab fa-instagram"></i></a>
                        <a href="https://youtube.com/user/infoscreening" class="w-8 h-8 rounded-lg bg-slate-800 hover:bg-[#3A07BB] flex items-center justify-center text-xs transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Col 2: Rubrikasi Artikel -->
                <div>
                    <h3 class="font-medium text-xs uppercase tracking-wider text-white mb-4">
                        Rubrikasi Artikel
                    </h3>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('categories.show', ['category' => 'berita']) }}" class="hover:text-white transition">Berita Perfilman</a></li>
                        <li><a href="{{ route('categories.show', ['category' => 'wawancara']) }}" class="hover:text-white transition">Wawancara Sineas</a></li>
                        <li><a href="{{ route('categories.show', ['category' => 'festival']) }}" class="hover:text-white transition">Festival Film</a></li>
                        <li><a href="{{ route('categories.show', ['category' => 'pemutaran']) }}" class="hover:text-white transition">Pemutaran Alternatif</a></li>
                        <li><a href="{{ route('categories.show', ['category' => 'kritik-film']) }}" class="hover:text-white transition">Kritik &amp; Esai Film</a></li>
                        <li><a href="{{ route('articles.index') }}" class="hover:text-[#a680f5] transition font-medium">Semua Arsip Tulisan &rarr;</a></li>
                    </ul>
                </div>

                <!-- Col 3: Ekosistem Sinema -->
                <div>
                    <h3 class="font-medium text-xs uppercase tracking-wider text-white mb-4">
                        Ekosistem Sinema
                    </h3>
                    <ul class="space-y-2 text-xs text-slate-400">
                        <li><a href="{{ route('events.index') }}" class="hover:text-white transition">Kalender Acara Sinema</a></li>
                        <li><a href="{{ route('contribute.create') }}" class="hover:text-white transition">Kirim Liputan / Naskah</a></li>
                        <li><a href="{{ route('directory.experts') }}" class="hover:text-white transition">Tenaga Ahli Film</a></li>
                        <li><a href="{{ route('directory.authors') }}" class="hover:text-white transition">Daftar Penulis (Authors)</a></li>
                        <li><a href="{{ route('directory.shop') }}" class="hover:text-white transition">Toko Zine &amp; Merchandise</a></li>
                        <li><a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="hover:text-white transition">Kontak Redaksi</a></li>
                    </ul>
                </div>

                <!-- Col 4: Topik Pilihan -->
                <div>
                    <h3 class="font-medium text-xs uppercase tracking-wider text-white mb-4">
                        Topik Pilihan
                    </h3>
                    <div class="flex flex-wrap gap-1.5">
                        @php
                            $tags = ['Film Indonesia', 'Sinema Rabu', 'Pemutaran Alternatif', 'Minikino', 'Kineforum', 'Film Dokumenter', 'Yogyakarta', 'Film Pendek', 'Jakarta', 'JAFF', 'Arthouse Cinema'];
                        @endphp
                        @foreach ($tags as $item)
                            <a href="{{ route('search.index', ['q' => $item]) }}" class="text-[11px] bg-slate-800/80 hover:bg-[#3A07BB] hover:text-white text-slate-300 px-2.5 py-1 rounded-md transition whitespace-nowrap">
                                #{{ $item }}
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Footer Bottom Bar -->
            <div class="mt-12 pt-6 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-normal">
                <p>&copy; {{ date('Y') }} Infoscreening.id. Festival Film dan Rupa-Rupa Sinema.</p>
                <div class="flex items-center gap-6">
                    <a href="{{ route('pages.show', ['slug' => 'kebijakan-privasi']) }}" class="hover:text-slate-300 transition">Kebijakan Privasi</a>
                    <a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="hover:text-slate-300 transition">Kontak Redaksi</a>
                    <a href="#top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="text-purple-300 hover:text-white flex items-center gap-1 font-medium">
                        <span>Ke Atas</span> <i class="fas fa-arrow-up text-[10px]"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>

</body>
</html>
