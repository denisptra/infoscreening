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
<body class="bg-[#f8fafc] text-slate-800 min-h-screen flex flex-col font-sans antialiased selection:bg-[#3A07BB] selection:text-white" x-data="{ mobileMenu: false }" @keydown.escape.window="mobileMenu = false">

    <!-- Top Info Bar (Translucent & Minimal) -->
    <div class="bg-[#190457]/90 backdrop-blur-md text-slate-300 text-xs border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-1.5 flex flex-wrap items-center justify-between gap-2.5">
            <div class="flex items-center gap-3 text-[11px] text-slate-300 whitespace-nowrap">
                <span class="flex items-center gap-1.5 text-purple-200">
                    <i class="far fa-calendar-alt text-xs"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </span>
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
                    @if(auth()->user()->canAccessCms())
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 font-medium text-amber-300 hover:text-white transition text-xs">
                            <i class="fas fa-gauge text-[11px]"></i> <span>Panel CMS</span>
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

                <!-- Center: Desktop Horizontal Menu -->
                <nav class="hidden lg:flex items-center space-x-2 text-[13px] font-normal text-white/80 whitespace-nowrap">
                    @foreach ($headerMenuItems as $item)
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false" @keydown.escape.window="open = false">
                            <div class="flex items-center">
                                @if ($item->url)
                                    <a
                                        href="{{ $item->url }}"
                                        @if ($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                        class="py-2 px-2.5 transition duration-200 hover:text-white"
                                    >{{ $item->label }}</a>
                                @elseif ($item->children->isNotEmpty())
                                    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="true" aria-controls="desktop-menu-{{ $item->id }}" class="py-2 px-2.5 transition duration-200 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 rounded-md">
                                        {{ $item->label }}
                                        <i class="fas fa-chevron-down ml-1 text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                @else
                                    <span class="py-2 px-2.5">{{ $item->label }}</span>
                                @endif

                                @if ($item->children->isNotEmpty() && $item->url)
                                    <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="true" aria-controls="desktop-menu-{{ $item->id }}" aria-label="Buka submenu {{ $item->label }}" class="py-2 pr-2.5 text-white/80 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 rounded-md">
                                        <i class="fas fa-chevron-down text-[9px] transition-transform duration-200" :class="open ? 'rotate-180' : ''"></i>
                                    </button>
                                @endif
                            </div>
                            @if ($item->children->isNotEmpty())
                                <div id="desktop-menu-{{ $item->id }}" x-show="open" x-cloak x-transition.origin.top.right class="absolute right-0 top-full z-50 w-56">
                                    <div class="overflow-hidden rounded-xl border border-slate-100 bg-white py-2 text-xs text-slate-700 shadow-xl">
                                        @foreach ($item->children as $child)
                                            @if ($child->url)
                                                <a
                                                    href="{{ $child->url }}"
                                                    @if ($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                                    class="block px-4 py-2 text-slate-600 transition hover:bg-slate-50 hover:text-[#3A07BB] focus-visible:outline-none focus-visible:bg-slate-50 focus-visible:text-[#3A07BB]"
                                                >{{ $child->label }}</a>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
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
            </div>
            @foreach ($headerMenuItems as $item)
                <div x-data="{ open: false }" @keydown.escape.window="open = false">
                    <div class="flex items-center justify-between gap-2">
                        @if ($item->url)
                            <a
                                href="{{ $item->url }}"
                                @if ($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                @click="mobileMenu = false"
                                class="block flex-1 py-2 text-white/90 transition hover:text-white"
                            >{{ $item->label }}</a>
                        @elseif ($item->children->isNotEmpty())
                            <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="true" aria-controls="mobile-menu-{{ $item->id }}" class="flex-1 py-2 text-left text-white/90 transition hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 rounded-md">
                                {{ $item->label }}
                                <i class="fas fa-chevron-down ml-1 text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                        @else
                            <span class="flex-1 py-2 text-white/90">{{ $item->label }}</span>
                        @endif
                        @if ($item->children->isNotEmpty() && $item->url)
                            <button type="button" @click="open = !open" :aria-expanded="open.toString()" aria-haspopup="true" aria-controls="mobile-menu-{{ $item->id }}" aria-label="Buka submenu {{ $item->label }}" class="px-2 py-2 text-white/80 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/70 rounded-md">
                                <i class="fas fa-chevron-down text-[10px] transition-transform" :class="open ? 'rotate-180' : ''"></i>
                            </button>
                        @endif
                    </div>
                    @if ($item->children->isNotEmpty())
                        <div id="mobile-menu-{{ $item->id }}" x-show="open" x-cloak x-transition class="space-y-1 border-l border-white/20 pl-4">
                            @foreach ($item->children as $child)
                                @if ($child->url)
                                    <a
                                        href="{{ $child->url }}"
                                        @if ($child->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                        @click="mobileMenu = false"
                                        class="block py-1.5 text-xs text-white/75 transition hover:text-white focus-visible:outline-none focus-visible:text-white"
                                    >{{ $child->label }}</a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
            @endforeach
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
    <footer id="site-footer" class="mt-20 border-t border-[#242638] bg-[#171824] text-[#9aabc2]">
        <div class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-x-10 gap-y-10 sm:grid-cols-2 lg:grid-cols-4">
                <div class="space-y-5">
                    <a href="{{ route('home') }}" class="inline-block">
                        <x-application-logo variant="white" class="h-7 w-auto opacity-95" />
                    </a>
                    <p class="max-w-sm text-sm leading-6 text-[#9aabc2]">{{ $footerAbout }}</p>
                    <nav aria-label="Media sosial" class="flex gap-2.5">
                        <a href="https://facebook.com" target="_blank" rel="noopener noreferrer" aria-label="Facebook" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22283a] text-white transition hover:bg-[#303950] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#5682ff]">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                        <a href="https://twitter.com/infoscreening" target="_blank" rel="noopener noreferrer" aria-label="X" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22283a] text-white transition hover:bg-[#303950] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#5682ff]">
                            <i class="fab fa-x-twitter" aria-hidden="true"></i>
                        </a>
                        <a href="https://instagram.com/infoscreening" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22283a] text-white transition hover:bg-[#303950] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#5682ff]">
                            <i class="fab fa-instagram" aria-hidden="true"></i>
                        </a>
                        <a href="https://youtube.com/user/infoscreening" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="flex h-10 w-10 items-center justify-center rounded-xl bg-[#22283a] text-white transition hover:bg-[#303950] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#5682ff]">
                            <i class="fab fa-youtube" aria-hidden="true"></i>
                        </a>
                    </nav>
                </div>

                <section aria-labelledby="footer-categories-heading">
                    <h2 id="footer-categories-heading" class="mb-4 text-sm font-semibold text-white">Rubrikasi Artikel</h2>
                    <ul class="space-y-2.5 text-sm">
                        @if ($footerRubricMenu)
                            @if ($footerRubricMenu->url)
                                <li>
                                    <a href="{{ $footerRubricMenu->url }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">{{ $footerRubricMenu->label }}</a>
                                </li>
                            @endif
                            @foreach ($footerRubricMenu->children as $item)
                                <li>
                                    <a
                                        href="{{ $item->url }}"
                                        @if ($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                        class="transition hover:text-white focus-visible:outline-none focus-visible:text-white"
                                    >{{ $item->label }}</a>
                                </li>
                            @endforeach
                            @foreach ($footerCategories as $category)
                                <li>
                                    <a href="{{ route('categories.show', ['category' => $category]) }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">{{ $category->name }}</a>
                                </li>
                            @endforeach
                            @if ($footerRubricMenu->children->isEmpty())
                                <li>
                                    <a href="{{ route('articles.index') }}" class="font-medium text-[#c2d0e5] transition hover:text-white focus-visible:outline-none focus-visible:text-white">Semua arsip tulisan</a>
                                </li>
                            @endif
                        @else
                            @forelse ($footerCategories as $category)
                                <li>
                                    <a href="{{ route('categories.show', ['category' => $category]) }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">{{ $category->name }}</a>
                                </li>
                            @empty
                                <li class="text-sm text-[#73829a]">Rubrik akan tampil setelah konten ditambahkan.</li>
                            @endforelse
                            <li>
                                <a href="{{ route('articles.index') }}" class="font-medium text-[#c2d0e5] transition hover:text-white focus-visible:outline-none focus-visible:text-white">Semua arsip tulisan</a>
                            </li>
                        @endif
                    </ul>
                </section>

                <section aria-labelledby="footer-ecosystem-heading">
                    <h2 id="footer-ecosystem-heading" class="mb-4 text-sm font-semibold text-white">Ekosistem Sinema</h2>
                    @if ($footerMenuItems->isNotEmpty())
                        <div class="space-y-4">
                            @foreach ($footerMenuItems as $group)
                                <div>
                                    @if ($group->url)
                                        <a
                                            href="{{ $group->url }}"
                                            @if ($group->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                            class="mb-2 inline-block text-sm font-medium text-[#c2d0e5] transition hover:text-white focus-visible:outline-none focus-visible:text-white"
                                        >{{ $group->label }}</a>
                                    @elseif ($group->children->isNotEmpty())
                                        <h3 class="mb-2 text-xs font-medium text-[#c2d0e5]">{{ $group->label }}</h3>
                                    @endif
                                    <ul class="space-y-2 text-sm">
                                        @foreach ($group->children as $item)
                                            <li>
                                                <a
                                                    href="{{ $item->url }}"
                                                    @if ($item->open_in_new_tab) target="_blank" rel="noopener noreferrer" @endif
                                                    class="transition hover:text-white focus-visible:outline-none focus-visible:text-white"
                                                >{{ $item->label }}</a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <ul class="space-y-2.5 text-sm">
                            <li><a href="{{ route('events.index') }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Kalender Acara Sinema</a></li>
                            <li><a href="{{ route('contribute.create') }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Kirim Liputan / Naskah</a></li>
                            <li><a href="{{ route('directory.experts') }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Tenaga Ahli Film</a></li>
                            <li><a href="{{ route('directory.authors') }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Daftar Penulis</a></li>
                            <li><a href="{{ route('directory.shop') }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Toko Zine &amp; Merchandise</a></li>
                            <li><a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Kontak Redaksi</a></li>
                        </ul>
                    @endif
                </section>

                <section aria-labelledby="footer-topics-heading">
                    <h2 id="footer-topics-heading" class="mb-4 text-sm font-semibold text-white">Topik Pilihan</h2>
                    <ul class="flex flex-wrap content-start gap-2">
                        @forelse ($footerTags as $tag)
                            <li>
                                <a href="{{ route('tags.show', ['tag' => $tag]) }}" class="inline-flex rounded-lg bg-[#22283a] px-3 py-1.5 text-xs text-[#c2d0e5] transition hover:bg-[#303950] hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#5682ff]">
                                    #{{ $tag->name }}
                                </a>
                            </li>
                        @empty
                            <li class="text-sm text-[#73829a]">Topik populer akan tampil setelah artikel diberi tag.</li>
                        @endforelse
                    </ul>
                </section>
            </div>

            <div class="mt-12 flex flex-col gap-4 border-t border-[#292b3d] pt-6 text-sm text-[#73829a] sm:flex-row sm:items-center sm:justify-between">
                <p>&copy; {{ date('Y') }} {{ $footerCopyright }}. Festival Film dan Rupa-Rupa Sinema.</p>
                <nav aria-label="Tautan footer" class="flex flex-wrap items-center gap-x-6 gap-y-3">
                    <a href="{{ route('pages.show', ['slug' => 'kebijakan-privasi']) }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Kebijakan Privasi</a>
                    <a href="{{ route('pages.show', ['slug' => 'kontak']) }}" class="transition hover:text-white focus-visible:outline-none focus-visible:text-white">Kontak Redaksi</a>
                    <a href="#top" onclick="window.scrollTo({top: 0, behavior: 'smooth'}); return false;" class="font-medium text-[#c7a6ff] transition hover:text-white focus-visible:outline-none focus-visible:text-white flex items-center gap-1">
                        <span>Ke Atas</span> <i class="fas fa-arrow-up text-[10px]"></i>
                    </a>
                </nav>
            </div>
        </div>
    </footer>

    <x-ui-feedback />
</body>
</html>
