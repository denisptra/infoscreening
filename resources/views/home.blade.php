@extends('layouts.site')

@section('title', 'Infoscreening - Festival Film dan Rupa-Rupa Sinema')
@section('description', 'Sinema Alternatif dan Banyak Lagi. Liputan festival film, pemutaran alternatif, berita, dan wawancara sineas Indonesia.')

@section('content')
<div class="space-y-10">

    <!-- 1. Sleek Modern Cinema Hero Banner -->
    <section class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-[#3A07BB] via-[#32069e] to-[#1c0260] text-white p-6 sm:p-10 shadow-lg border border-purple-500/20">
        <!-- Subtle Background Glow Elements & Transparent Emblem Watermark -->
        <div class="absolute -right-16 -top-16 w-80 h-80 rounded-full bg-purple-500/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-16 -bottom-16 w-80 h-80 rounded-full bg-indigo-500/20 blur-3xl pointer-events-none"></div>
        <img
            src="{{ asset('images/emblem-white.png') }}"
            alt=""
            loading="lazy"
            decoding="async"
            class="absolute -right-6 -bottom-8 w-64 md:w-80 h-auto opacity-10 pointer-events-none select-none"
        >

        <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
            <!-- Left Info & CTA -->
            <div class="lg:col-span-7 space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur-sm border border-white/15 text-[11px] font-normal text-purple-100 tracking-wide">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Festival Film dan Rupa-Rupa Sinema</span>
                </div>

                <h1 class="font-sans font-medium text-2xl sm:text-3xl lg:text-4xl leading-tight text-white tracking-tight">
                    Merayakan Ragam Cerita, Ruang Putar, dan Dinamika Sinema Mandiri
                </h1>

                <p class="text-xs sm:text-sm text-purple-100/90 leading-relaxed max-w-xl font-normal">
                    Media editorial dan penemuan ekosistem film alternatif Indonesia sejak 2012. Temukan ulasan festival, catatan pemutaran komunitas, hingga wawancara eksklusif sineas tanah air.
                </p>

                <div class="flex flex-wrap items-center gap-3 pt-2">
                    <a
                        href="{{ route('events.index') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white text-[#3A07BB] hover:bg-purple-50 text-xs font-medium tracking-wide transition shadow-sm hover:shadow"
                    >
                        <i class="far fa-calendar-check text-xs"></i>
                        <span>Lihat Kalender Pemutaran</span>
                    </a>
                    <a
                        href="{{ route('contribute.create') }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-white/15 hover:bg-white/20 text-white text-xs font-normal border border-white/20 transition"
                    >
                        <span>Kirim Liputan / Naskah</span>
                        <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>
            </div>

            <!-- Right: Featured Spotlight Highlight Card -->
            @if ($featured)
            <div class="lg:col-span-5">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 shadow-xl hover:bg-white/15 transition duration-300 group">
                    <div class="relative overflow-hidden rounded-xl h-48 sm:h-52 mb-3 bg-black/20">
                        <img
                            src="{{ $featured->image_url }}"
                            alt="{{ $featured->title }}"
                            decoding="async"
                            fetchpriority="high"
                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                        >
                        @if ($featured->primary_category)
                            <div class="absolute top-3 left-3">
                                <span class="bg-[#3A07BB]/90 text-white text-[11px] font-medium px-2.5 py-0.5 rounded-md backdrop-blur-sm">
                                    {{ $featured->primary_category->name }}
                                </span>
                            </div>
                        @endif
                        <div class="absolute bottom-2.5 right-2.5 bg-black/70 backdrop-blur-sm text-white text-[10px] px-2.5 py-0.5 rounded-md flex items-center gap-1.5 whitespace-nowrap">
                            <i class="far fa-eye text-purple-300"></i>
                            <span>{{ number_format($featured->views) }} views</span>
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-[10px] uppercase tracking-wider text-purple-200 font-medium">Liputan Utama</span>
                        <h2 class="font-sans font-medium text-sm sm:text-base text-white group-hover:text-purple-200 transition line-clamp-2 leading-snug">
                            <a href="{{ route('articles.show', ['article' => $featured]) }}">
                                {{ $featured->title }}
                            </a>
                        </h2>
                        <p class="text-xs text-purple-100/80 line-clamp-2 font-normal leading-relaxed">
                            {{ $featured->excerpt }}
                        </p>
                    </div>

                    <div class="mt-3 pt-3 border-t border-white/15 flex items-center justify-between text-[11px] text-purple-200 whitespace-nowrap">
                        <span>{{ $featured->author->name ?? 'Redaksi' }}</span>
                        <a href="{{ route('articles.show', ['article' => $featured]) }}" class="text-white hover:underline flex items-center gap-1 font-medium">
                            <span>Baca Selengkapnya</span>
                            <i class="fas fa-angle-right text-[10px]"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </section>

    <!-- 2. Spotlight Row (3 Modern Cards) -->
    @if ($spotlight->isNotEmpty())
    <section>
        <div class="flex items-center justify-between mb-4">
            <h2 class="font-sans font-semibold text-base sm:text-lg text-slate-800 tracking-tight">
                Sorotan Berita &amp; Artikel
            </h2>
            <a href="{{ route('articles.index') }}" class="text-xs text-[#3A07BB] hover:underline font-medium whitespace-nowrap">
                Lihat Semua &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($spotlight as $item)
                <article class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm hover:shadow-card-hover hover:border-purple-300 transition-all duration-300 flex flex-col justify-between group">
                    <div>
                        <div class="relative overflow-hidden rounded-xl h-44 mb-3.5 bg-slate-100">
                            <a href="{{ route('articles.show', ['article' => $item]) }}">
                                <img
                                    src="{{ $item->image_url }}"
                                    alt="{{ $item->title }}"
                                    loading="lazy"
                                    decoding="async"
                                    class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                >
                            </a>
                            @if ($item->primary_category)
                                <div class="absolute top-2.5 left-2.5">
                                    <span class="badge-cat text-[10px]">
                                        {{ $item->primary_category->name }}
                                    </span>
                                </div>
                            @endif
                            <div class="absolute bottom-2 right-2 bg-black/70 backdrop-blur-sm text-white text-[10px] px-2 py-0.5 rounded-md flex items-center gap-1 whitespace-nowrap">
                                <i class="far fa-eye text-purple-300"></i>
                                <span>{{ number_format($item->views) }}</span>
                            </div>
                        </div>

                        <div class="space-y-1.5">
                            <h3 class="font-sans font-medium text-sm sm:text-base text-slate-800 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                                <a href="{{ route('articles.show', ['article' => $item]) }}">
                                    {{ $item->title }}
                                </a>
                            </h3>
                            <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed font-normal">
                                {{ $item->excerpt }}
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 whitespace-nowrap">
                        <span class="truncate max-w-[120px]">{{ $item->author->name ?? 'Redaksi' }}</span>
                        <time datetime="{{ $item->published_at?->toDateString() }}">
                            {{ $item->published_at?->translatedFormat('d M Y') }}
                        </time>
                    </div>
                </article>
            @endforeach
        </div>
    </section>
    @endif

    <!-- 3. Main Two-Column Layout: Article Feed (8 cols) & Magazine Sidebar (4 cols) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        <!-- Left Column: Articles Feed (8 cols) -->
        <div class="lg:col-span-8 space-y-8">

            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                    <div>
                        <h2 class="font-sans font-semibold text-lg text-slate-800 tracking-tight">
                            Artikel &amp; Liputan Terbaru
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5 font-normal">
                            Wawasan, ulasan pemutaran, dan kabar ekosistem film independen.
                        </p>
                    </div>
                    <a href="{{ route('articles.index') }}" class="text-xs text-[#3A07BB] hover:underline font-medium whitespace-nowrap">
                        Arsip Lengkap &rarr;
                    </a>
                </div>

                <!-- Articles Grid with Modern Horizontal-Friendly Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach ($articles as $article)
                        <article class="bg-white rounded-xl border border-slate-200/70 p-3.5 hover:border-purple-300 hover:shadow-card-hover transition-all duration-300 flex flex-col justify-between group">
                            <div>
                                <div class="relative overflow-hidden rounded-lg h-40 mb-3 bg-slate-100">
                                    <a href="{{ route('articles.show', ['article' => $article]) }}">
                                        <img
                                            src="{{ $article->image_url }}"
                                            alt="{{ $article->title }}"
                                            loading="lazy"
                                            decoding="async"
                                            class="w-full h-full object-cover group-hover:scale-105 transition duration-500"
                                        >
                                    </a>
                                    @if ($article->primary_category)
                                        <div class="absolute top-2 left-2">
                                            <span class="badge-cat text-[10px]">
                                                {{ $article->primary_category->name }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="absolute bottom-2 right-2 bg-black/70 backdrop-blur-sm text-white text-[10px] px-2 py-0.5 rounded-md flex items-center gap-1 whitespace-nowrap">
                                        <i class="far fa-eye text-purple-300"></i>
                                        <span>{{ number_format($article->views) }}</span>
                                    </div>
                                </div>

                                <div class="space-y-1.5">
                                    <h3 class="font-sans font-medium text-sm text-slate-800 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                                        <a href="{{ route('articles.show', ['article' => $article]) }}">
                                            {{ $article->title }}
                                        </a>
                                    </h3>
                                    <p class="text-xs text-slate-500 line-clamp-2 font-normal leading-relaxed">
                                        {{ $article->excerpt }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400 whitespace-nowrap">
                                <span class="truncate max-w-[120px]">{{ $article->author->name ?? 'Redaksi' }}</span>
                                <time>{{ $article->published_at?->translatedFormat('d M Y') }}</time>
                            </div>
                        </article>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8 pt-4 border-t border-slate-100">
                    {{ $articles->links() }}
                </div>
            </div>

            <!-- Callout Card: Berkontribusi -->
            <div class="rounded-2xl bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200/80 p-6 flex flex-col sm:flex-row items-center justify-between gap-6 shadow-sm">
                <div class="space-y-1.5 text-left">
                    <span class="text-[10px] font-semibold uppercase tracking-wider text-[#3A07BB] bg-purple-100 px-2 py-0.5 rounded">
                        Kirim Tulisan
                    </span>
                    <h3 class="font-sans font-semibold text-lg text-slate-900">
                        Punya Kabar Pemutaran atau Resensi Film?
                    </h3>
                    <p class="text-xs text-slate-600 max-w-lg font-normal leading-relaxed">
                        Infoscreening menerima naskah ulasan, agenda festival kampus, maupun liputan bioskop mandiri dari seluruh kota di Indonesia.
                    </p>
                </div>
                <a
                    href="{{ route('contribute.create') }}"
                    class="shrink-0 px-5 py-2.5 rounded-xl bg-[#3A07BB] hover:bg-[#2e0594] text-white text-xs font-medium tracking-wide shadow transition whitespace-nowrap"
                >
                    Kirim Kontribusi &rarr;
                </a>
            </div>

        </div>

        <!-- Right Column: Sidebar (4 cols) -->
        <aside class="lg:col-span-4 space-y-6">

            <!-- 1. Widget Most Popular (Ordered 1-6) -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h3 class="font-sans font-semibold text-sm text-slate-800 flex items-center gap-2">
                        <i class="fas fa-fire text-amber-500 text-xs"></i>
                        <span>Paling Populer</span>
                    </h3>
                    <span class="text-[10px] text-slate-400 font-normal">Views</span>
                </div>

                <div class="space-y-3">
                    @foreach ($popularArticles as $index => $pop)
                        <div class="flex items-start gap-3 group pb-3 border-b border-slate-100 last:border-b-0 last:pb-0">
                            <span class="w-6 h-6 rounded-lg bg-purple-50 text-[#3A07BB] font-medium text-xs flex items-center justify-center shrink-0">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 text-[10px] text-slate-400 mb-0.5 whitespace-nowrap">
                                    <span class="font-medium text-[#3A07BB]">{{ $pop->primary_category->name ?? 'Film' }}</span>
                                    <span>&bull;</span>
                                    <span class="text-emerald-700 font-medium">{{ number_format($pop->views) }} views</span>
                                </div>
                                <h4 class="font-sans font-normal text-xs text-slate-800 group-hover:text-[#3A07BB] transition line-clamp-2 leading-snug">
                                    <a href="{{ route('articles.show', ['article' => $pop]) }}">
                                        {{ $pop->title }}
                                    </a>
                                </h4>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- 2. Widget Agenda Kalender Sinema -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-100">
                    <h3 class="font-sans font-semibold text-sm text-slate-800 flex items-center gap-2">
                        <i class="far fa-calendar-alt text-[#3A07BB] text-xs"></i>
                        <span>Agenda Terdekat</span>
                    </h3>
                    <a href="{{ route('events.index') }}" class="text-[11px] text-[#3A07BB] hover:underline font-medium">Semua</a>
                </div>

                <div class="space-y-3.5">
                    @forelse ($upcomingEvents as $event)
                        <div class="border-l-2 border-[#3A07BB] pl-3 py-0.5 group">
                            <div class="flex items-center gap-1.5 text-[10px] text-purple-700 font-medium whitespace-nowrap">
                                <span>{{ $event->type }}</span>
                                @if($event->city)
                                    <span>&bull;</span>
                                    <span>{{ $event->city }}</span>
                                @endif
                            </div>
                            <h4 class="font-sans font-normal text-xs text-slate-800 group-hover:text-[#3A07BB] transition mt-0.5 line-clamp-1">
                                <a href="{{ route('events.show', ['event' => $event]) }}">
                                    {{ $event->title }}
                                </a>
                            </h4>
                            <div class="text-[11px] text-slate-400 mt-1 flex items-center gap-2 whitespace-nowrap">
                                <span><i class="far fa-clock mr-1 text-[#3A07BB]"></i>{{ $event->starts_at->translatedFormat('d M Y') }}</span>
                                @if($event->venue)
                                    <span>&bull;</span>
                                    <span class="truncate max-w-[110px]">{{ $event->venue }}</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 italic font-normal">Belum ada agenda terdekat.</p>
                    @endforelse
                </div>

                <div class="mt-4 pt-3 border-t border-slate-100">
                    <a href="{{ route('events.index') }}" class="block text-center text-xs text-[#3A07BB] hover:underline font-medium">
                        Buka Kalender Sinema &rarr;
                    </a>
                </div>
            </div>

            <!-- 3. Widget Popular Tag Cloud -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <div class="pb-3 mb-3 border-b border-slate-100">
                    <h3 class="font-sans font-semibold text-sm text-slate-800 flex items-center gap-2">
                        <i class="fas fa-tags text-[#3A07BB] text-xs"></i>
                        <span>Topik Sinema</span>
                    </h3>
                </div>

                <div class="flex flex-wrap gap-1.5">
                    @foreach ($popularTags as $tag)
                        <a href="{{ route('tags.show', ['tag' => $tag]) }}" class="text-[11px] bg-slate-100 hover:bg-[#3A07BB] hover:text-white text-slate-600 px-2.5 py-1 rounded-md transition whitespace-nowrap font-normal">
                            #{{ $tag->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- 4. Widget Tentang Infoscreening (With Official Logo) -->
            <div class="rounded-2xl bg-white border border-slate-200/80 p-5 shadow-sm space-y-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-purple.png') }}" alt="Infoscreening" loading="lazy" decoding="async" class="h-6 w-auto object-contain">
                </div>
                <p class="text-xs text-slate-600 leading-relaxed font-normal">
                    Dimulai dari kegemaran menonton film-film pada festival film dan pemutaran alternatif, InfoScreening merupakan media informasi pemutaran alternatif dan festival film di Indonesia sejak akun Twitter @InfoScreening dibuat pada tahun 2012.
                </p>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-xs text-[#3A07BB] font-medium">
                    <a href="{{ route('pages.show', ['slug' => 'tentang-kami']) }}" class="hover:underline">Tentang Kami &rarr;</a>
                    <a href="{{ route('directory.authors') }}" class="hover:underline">Daftar Penulis &rarr;</a>
                </div>
            </div>

        </aside>
    </div>

</div>
@endsection
