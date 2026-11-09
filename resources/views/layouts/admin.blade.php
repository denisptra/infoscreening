<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('page_title', 'Dashboard') - Infoscreening CMS</title>

    <!-- Tab Favicon (Emblem) -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

    <!-- Self-hosted font preload -->
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
@php
    $currentUser = auth()->user();
    $sidebarCounts = \Illuminate\Support\Facades\Cache::remember('admin.sidebar.counts.'.$currentUser->id, now()->addMinute(), fn () => [
        'pending' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Contribution::where('status', 'pending')->count() : 0,
        'articles' => \App\Models\Article::query()
            ->when($currentUser->isEditor(), fn ($query) => $query->where('user_id', $currentUser->id))
            ->count(),
        'categories' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Category::count() : 0,
        'tags' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Tag::count() : 0,
        'events' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Event::count() : 0,
        'experts' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Expert::count() : 0,
        'products' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Product::count() : 0,
        'media' => in_array($currentUser->role, [\App\Enums\UserRole::SuperAdmin, \App\Enums\UserRole::Admin], true) ? \App\Models\Media::count() : 0,
    ]);
@endphp

<body class="bg-[#f6f6f9] text-[#32324d] font-sans antialiased min-h-screen flex" x-data="{ sidebarOpen: false, notificationsOpen: false }">

    <!-- Mobile Overlay Backdrop -->
    <div
        x-show="sidebarOpen"
        x-cloak
        @click="sidebarOpen = false"
        class="fixed inset-0 z-40 bg-black/60 lg:hidden backdrop-blur-xs"
    ></div>

    <!-- Strapi Iconic Dark Sidebar (#212134) -->
    <aside
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-[#212134] text-[#c0c0cf] flex flex-col justify-between transition-transform duration-200 lg:translate-x-0 border-r border-[#181826] select-none"
    >
        <div class="flex-1 overflow-y-auto">
            <!-- Strapi Header Brand with Transparent Emblem -->
            <div class="h-16 px-4 flex items-center justify-between border-b border-[#2d2d44]">
                <a href="{{ route('strapi.dashboard') }}" class="flex items-center gap-2.5">
                    <img
                        src="{{ asset('images/emblem-white.png') }}"
                        alt="Infoscreening"
                        class="w-7 h-auto object-contain shrink-0"
                    >
                    <div class="flex flex-col min-w-0">
                        <span class="font-medium text-xs text-white leading-tight truncate">INFOSCREENING</span>
                        <span class="text-[10px] text-[#8e8ea9] tracking-wider uppercase">Panel Editorial</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-[#8e8ea9] hover:text-white p-1">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Strapi Sidebar Navigation -->
            <nav class="p-3 space-y-5 text-xs">

                <!-- Section: GENERAL -->
                <div class="space-y-1">
                    <div class="px-3 pb-1 text-[10px] font-semibold text-[#8e8ea9] tracking-wider uppercase">
                        General
                    </div>
                    <a
                        href="{{ route('strapi.dashboard') }}"
                        class="strapi-sidebar-item {{ request()->routeIs('strapi.dashboard') ? 'active' : '' }}"
                    >
                        <i class="fas fa-house text-[#8e8ea9] w-4 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    @if (! auth()->user()->isEditor())
                        <a
                            href="{{ route('strapi.contributions.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.contributions.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-inbox text-[#8e8ea9] w-4 text-center"></i>
                            <span>Submissions Inbox</span>
                            @if ($sidebarCounts['pending'] > 0)
                                <span class="strapi-badge-count">{{ $sidebarCounts['pending'] }}</span>
                            @endif
                        </a>
                    @endif
                </div>

                <!-- Section: CONTENT MANAGER (Collection Types) -->
                <div class="space-y-1">
                    <div class="px-3 pb-1 text-[10px] font-semibold text-[#8e8ea9] tracking-wider uppercase flex items-center justify-between">
                        <span>Content Manager</span>
                        <span class="text-[9px] text-[#666687] font-normal">Collections</span>
                    </div>

                    <a
                        href="{{ route('strapi.articles.index') }}"
                        class="strapi-sidebar-item {{ request()->routeIs('strapi.articles.*') ? 'active' : '' }}"
                    >
                        <i class="far fa-newspaper text-[#8e8ea9] w-4 text-center"></i>
                        <span>Articles</span>
                        <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['articles'] }}</span>
                    </a>

                    @if (! auth()->user()->isEditor())
                        <a
                            href="{{ route('strapi.categories.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.categories.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-folder text-[#8e8ea9] w-4 text-center"></i>
                            <span>Categories</span>
                            <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['categories'] }}</span>
                        </a>

                        <a
                            href="{{ route('strapi.tags.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.tags.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-tag text-[#8e8ea9] w-4 text-center"></i>
                            <span>Tags</span>
                            <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['tags'] }}</span>
                        </a>

                        <a
                            href="{{ route('strapi.events.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.events.*') ? 'active' : '' }}"
                        >
                            <i class="far fa-calendar-alt text-[#8e8ea9] w-4 text-center"></i>
                            <span>Calendar Events</span>
                            <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['events'] }}</span>
                        </a>

                        <a
                            href="{{ route('strapi.experts.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.experts.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-user-tie text-[#8e8ea9] w-4 text-center"></i>
                            <span>Experts (Rupa Jasa)</span>
                            <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['experts'] }}</span>
                        </a>

                        <a
                            href="{{ route('strapi.products.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.products.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-bag-shopping text-[#8e8ea9] w-4 text-center"></i>
                            <span>Shop Products</span>
                            <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['products'] }}</span>
                        </a>
                    @endif
                </div>

                <!-- Section: PLUGINS & TOOLS -->
                @if (! auth()->user()->isEditor())
                    <div class="space-y-1">
                    <div class="px-3 pb-1 text-[10px] font-semibold text-[#8e8ea9] tracking-wider uppercase">
                        Plugins
                    </div>
                    @if (auth()->user()->isSuperAdmin())
                        <a
                            href="{{ route('strapi.content-types.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.content-types.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-cubes text-[#8e8ea9] w-4 text-center"></i>
                            <span>Content-Type Builder</span>
                        </a>
                    @endif
                    <a
                        href="{{ route('strapi.media.index') }}"
                        class="strapi-sidebar-item {{ request()->routeIs('strapi.media.*') ? 'active' : '' }}"
                    >
                        <i class="fas fa-images text-[#8e8ea9] w-4 text-center"></i>
                        <span>Media Library</span>
                        <span class="text-[10px] text-[#8e8ea9] ml-auto font-normal">{{ $sidebarCounts['media'] }}</span>
                    </a>
                    </div>
                @endif

                <!-- Section: SETTINGS & APPEARANCE -->
                @if (auth()->user()->isSuperAdmin() || auth()->user()->isEditorialAdmin())
                    <div class="space-y-1">
                    <div class="px-3 pb-1 text-[10px] font-semibold text-[#8e8ea9] tracking-wider uppercase">
                        Tampilan &amp; Pengaturan
                    </div>
                    <a
                        href="{{ route('strapi.navigation.index') }}"
                        class="strapi-sidebar-item {{ request()->routeIs('strapi.navigation.*', 'strapi.appearance.*') ? 'active' : '' }}"
                    >
                        <i class="fas fa-bars-staggered text-[#8e8ea9] w-4 text-center"></i>
                        <span>Navigasi &amp; Footer</span>
                    </a>
                    @if (auth()->user()->isSuperAdmin())
                        <a
                            href="{{ route('strapi.settings.index') }}"
                            class="strapi-sidebar-item {{ request()->routeIs('strapi.settings.*') ? 'active' : '' }}"
                        >
                            <i class="fas fa-gear text-[#8e8ea9] w-4 text-center"></i>
                            <span>Settings &amp; API</span>
                        </a>
                    @endif
                    <a
                        href="{{ route('home') }}"
                        target="_blank"
                        class="strapi-sidebar-item text-[#a680f5] hover:text-white"
                    >
                        <i class="fas fa-arrow-up-right-from-square text-[#a680f5] w-4 text-center"></i>
                        <span>Live Portal View</span>
                    </a>
                </div>
                @endif

            </nav>
        </div>

        <!-- Strapi Sidebar Bottom User Profile -->
        <div class="p-3 border-t border-[#2d2d44] bg-[#1d1d2e]">
            <div class="flex items-center justify-between px-2 py-1.5">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-[#3A07BB] text-white flex items-center justify-center text-xs font-semibold shrink-0">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                    <div class="flex flex-col min-w-0">
                        <span class="text-xs font-medium text-white truncate">{{ auth()->user()->name ?? 'Administrator' }}</span>
                        <span class="text-[10px] text-[#8e8ea9]">{{ auth()->user()->role->label() }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-[#8e8ea9] hover:text-red-400 p-1.5 transition rounded" title="Logout">
                        <i class="fas fa-arrow-right-from-bracket text-xs"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- Main Strapi Content Area -->
    <div class="flex-1 flex flex-col min-w-0 lg:ml-64">

        <!-- Strapi Clean White Top Header Bar -->
        <header class="fixed top-0 right-0 left-0 lg:left-64 z-30 h-16 bg-white border-b border-[#eaeaef] flex items-center justify-between px-4 sm:px-6 shadow-[0_1px_4px_rgba(33,33,52,0.04)]">
            <!-- Left: Breadcrumb / Page Title -->
            <div class="flex items-center gap-3">
                <button @click="sidebarOpen = true" class="lg:hidden p-1.5 text-slate-500 hover:text-slate-800">
                    <i class="fas fa-bars text-base"></i>
                </button>
                <div class="flex items-center gap-2 text-xs text-[#666687]">
                    <span class="font-normal text-[#8e8ea9]">Strapi</span>
                    <span class="text-[#dcdce4]">/</span>
                    <h1 class="font-medium text-sm text-[#32324d]">
                        @yield('page_title', 'Dashboard')
                    </h1>
                </div>
            </div>

            <!-- Right: Actions & Profile -->
            <div class="flex items-center gap-3">
                <!-- Strapi "Visit Website" Button -->
                <a
                    href="{{ route('home') }}"
                    target="_blank"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-[#4945ff] bg-[#f0ebfc] hover:bg-[#ded0fc] rounded-md transition"
                >
                    <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                    <span>Live Portal</span>
                </a>

                <div class="h-5 w-px bg-[#eaeaef] hidden sm:block"></div>

                <div class="relative" @keydown.escape.window="notificationsOpen = false" @click.outside="notificationsOpen = false">
                    <button
                        type="button"
                        @click="notificationsOpen = !notificationsOpen"
                        :aria-expanded="notificationsOpen.toString()"
                        aria-controls="admin-notifications-panel"
                        aria-label="Buka notifikasi"
                        class="relative p-2 text-[#666687] hover:text-[#32324d] rounded-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[#4945ff]"
                    >
                        <i class="far fa-bell text-sm" aria-hidden="true"></i>
                        @if ($notificationCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 min-w-4 h-4 px-1 rounded-full bg-[#4945ff] text-white text-[9px] leading-4 text-center font-semibold">
                                {{ $notificationCount > 9 ? '9+' : $notificationCount }}
                            </span>
                        @endif
                    </button>

                    <section
                        id="admin-notifications-panel"
                        x-show="notificationsOpen"
                        x-cloak
                        x-transition.origin.top.right
                        class="absolute right-0 top-full mt-2 w-80 max-w-[calc(100vw-2rem)] overflow-hidden rounded-lg border border-[#eaeaef] bg-white shadow-xl"
                        aria-label="Notifikasi"
                    >
                        <div class="flex items-start justify-between gap-3 border-b border-[#eaeaef] px-4 py-3">
                            <div>
                                <h2 class="text-sm font-semibold text-[#32324d]">Notifikasi</h2>
                                <p class="mt-0.5 text-xs text-[#8e8ea9]">Hal yang perlu ditinjau</p>
                            </div>
                            @if ($notificationCount > 0)
                                <span class="rounded-full bg-[#f0ebfc] px-2 py-1 text-[10px] font-medium text-[#4945ff]">
                                    {{ $notificationCount }} tugas
                                </span>
                            @endif
                        </div>

                        @if ($notifications->isNotEmpty())
                            <ul class="max-h-[60vh] overflow-y-auto divide-y divide-[#f0f0f4]">
                                @foreach ($notifications as $notification)
                                    <li>
                                        <a
                                            href="{{ $notification['url'] }}"
                                            @click="notificationsOpen = false"
                                            class="flex gap-3 px-4 py-3 hover:bg-[#f8f7fc] focus-visible:outline-none focus-visible:bg-[#f8f7fc]"
                                        >
                                            <span class="mt-0.5 flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-[#f0ebfc] text-[#4945ff]">
                                                <i class="far fa-bell text-xs" aria-hidden="true"></i>
                                            </span>
                                            <span class="min-w-0">
                                                <span class="block text-xs font-medium text-[#32324d]">{{ $notification['title'] }}</span>
                                                <span class="mt-1 block truncate text-xs text-[#666687]">{{ $notification['description'] }}</span>
                                                <span class="mt-1 block text-[10px] text-[#8e8ea9]">{{ $notification['createdAt']->diffForHumans() }}</span>
                                            </span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                            <a
                                href="{{ $notificationUrl }}"
                                class="block border-t border-[#eaeaef] px-4 py-2.5 text-center text-xs font-medium text-[#4945ff] hover:bg-[#f8f7fc] focus-visible:outline-none focus-visible:bg-[#f8f7fc]"
                            >
                                Buka daftar
                            </a>
                        @else
                            <p class="px-4 py-6 text-center text-xs text-[#666687]">{{ $emptyNotificationMessage }}</p>
                        @endif
                    </section>
                </div>

                <!-- Quick Profile Badge -->
                <div class="flex items-center gap-2 pl-2">
                    <div class="w-7 h-7 rounded-full bg-[#3A07BB] text-white flex items-center justify-center text-xs font-medium">
                        {{ substr(auth()->user()->name ?? 'A', 0, 1) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Strapi Canvas Page Body -->
        <main class="flex-1 px-6 pb-6 pt-24 sm:px-8 sm:pb-8 sm:pt-24 space-y-6 max-w-7xl w-full mx-auto">
            @if (session('status'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 p-3.5 rounded-lg text-xs font-medium flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-check-circle text-emerald-600"></i>
                        <span>{{ session('status') }}</span>
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <x-ui-feedback />
</body>
</html>
