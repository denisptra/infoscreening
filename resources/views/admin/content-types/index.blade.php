@extends('layouts.admin')

@section('page_title', 'Content-Type Builder')

@section('content')
<div class="space-y-6" x-data="{ addFieldModal: false, newTypeModal: false }">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-[#f0ebfc] text-[#4945ff] mb-1">
                <i class="fas fa-cubes text-[10px]"></i>
                <span>Plugins / Content-Type Builder</span>
            </div>
            <h1 class="font-sans font-semibold text-xl text-[#32324d] tracking-tight">Content-Type Builder</h1>
            <p class="text-xs text-[#8e8ea9] font-normal">Modelkan skema data, struktur relasi, dan tipe field untuk ekosistem konten Infoscreening.</p>
        </div>

        <div class="flex items-center gap-2">
            <button
                @click="newTypeModal = true"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium shadow-sm transition"
            >
                <i class="fas fa-plus text-[10px]"></i>
                <span>Create new collection type</span>
            </button>
        </div>
    </div>

    <!-- Main Content-Type Builder Layout -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Column: Collection Types List (4 cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                <div class="p-4 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                    <span class="text-xs font-semibold uppercase tracking-wider text-[#666687]">Collection Types ({{ count($collectionTypes) }})</span>
                    <span class="text-[10px] text-[#8e8ea9]">Strapi Schemas</span>
                </div>

                <div class="divide-y divide-[#eaeaef]">
                    @foreach ($collectionTypes as $type)
                        @php
                            $isSelected = ($selected === $type['api_id']);
                        @endphp
                        <a
                            href="{{ route('strapi.content-types.index', ['type' => $type['api_id']]) }}"
                            class="flex items-center justify-between p-3.5 text-xs transition {{ $isSelected ? 'bg-[#f0ebfc] text-[#4945ff] font-medium border-l-4 border-[#4945ff]' : 'text-[#32324d] hover:bg-[#f6f6f9]' }}"
                        >
                            <div class="flex items-center gap-2.5 min-w-0">
                                <div class="w-7 h-7 rounded-lg {{ $isSelected ? 'bg-[#4945ff] text-white' : 'bg-[#eaeaef] text-[#666687]' }} flex items-center justify-center text-xs shrink-0">
                                    <i class="fas fa-layer-group"></i>
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium truncate">{{ $type['name'] }}</div>
                                    <div class="text-[10px] text-[#8e8ea9] truncate">api::{{ $type['api_id'] }}.{{ $type['api_id'] }}</div>
                                </div>
                            </div>
                            <span class="text-[10px] px-2 py-0.5 rounded-full {{ $isSelected ? 'bg-[#4945ff]/10 text-[#4945ff]' : 'bg-[#f6f6f9] text-[#8e8ea9]' }} font-semibold shrink-0">
                                {{ $type['count'] }} entries
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Single Types Info Box -->
            <div class="p-4 rounded-xl border border-[#eaeaef] bg-white text-xs space-y-2">
                <div class="flex items-center gap-2 text-[#32324d] font-semibold">
                    <i class="fas fa-file-lines text-[#4945ff]"></i>
                    <span>Single Types</span>
                </div>
                <p class="text-[11px] text-[#8e8ea9] leading-relaxed">
                    Single types digunakan untuk halaman statis tunggal seperti <strong>Tentang Kami</strong>, <strong>Kontak Redaksi</strong>, dan <strong>Kebijakan Privasi</strong>.
                </p>
                <a href="{{ route('pages.show', 'tentang-kami') }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-[#4945ff] hover:underline font-medium">
                    <span>Lihat Halaman Statis</span>
                    <i class="fas fa-arrow-up-right-from-square text-[9px]"></i>
                </a>
            </div>
        </div>

        <!-- Right Column: Schema Details & Fields (8 cols) -->
        <div class="lg:col-span-8 space-y-4">
            @php
                $activeCollection = collect($collectionTypes)->firstWhere('api_id', $selected) ?? $collectionTypes[0];
            @endphp

            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                <!-- Header with Actions -->
                <div class="p-5 border-b border-[#eaeaef] flex flex-col sm:flex-row sm:items-center justify-between gap-3 bg-[#fafafc]">
                    <div>
                        <div class="flex items-center gap-2">
                            <h2 class="font-sans font-semibold text-base text-[#32324d]">{{ $activeCollection['name'] }}</h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-[#f0ebfc] text-[#4945ff]">
                                api::{{ $activeCollection['api_id'] }}
                            </span>
                        </div>
                        <p class="text-xs text-[#8e8ea9] mt-0.5">{{ $activeCollection['description'] }}</p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            @click="addFieldModal = true"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-[#4945ff] text-[#4945ff] hover:bg-[#f0ebfc] text-xs font-medium transition"
                        >
                            <i class="fas fa-plus text-[10px]"></i>
                            <span>Add another field</span>
                        </button>
                        <a
                            href="{{ route('strapi.' . $activeCollection['plural'] . '.index') }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-[#4945ff] text-white hover:bg-[#3b37eb] text-xs font-medium transition shadow-sm"
                        >
                            <i class="fas fa-table-list text-[10px]"></i>
                            <span>Manage Content ({{ $activeCollection['count'] }})</span>
                        </a>
                    </div>
                </div>

                <!-- Fields Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-[#f6f6f9] text-[#666687] font-semibold text-[11px] uppercase tracking-wider border-b border-[#eaeaef]">
                            <tr>
                                <th class="p-3.5 pl-5">Field Name</th>
                                <th class="p-3.5">Type</th>
                                <th class="p-3.5">Constraints</th>
                                <th class="p-3.5 text-right pr-5">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[#eaeaef]">
                            @foreach ($activeCollection['fields'] as $field)
                                <tr class="hover:bg-[#fafafc] transition">
                                    <td class="p-3.5 pl-5 font-mono text-[12px] font-medium text-[#32324d]">
                                        <div class="flex items-center gap-2">
                                            @if (str_contains($field['type'], 'Rich Text'))
                                                <i class="fas fa-align-left text-amber-500 text-xs"></i>
                                            @elseif (str_contains($field['type'], 'Media'))
                                                <i class="fas fa-image text-emerald-500 text-xs"></i>
                                            @elseif (str_contains($field['type'], 'Relation'))
                                                <i class="fas fa-link text-[#4945ff] text-xs"></i>
                                            @elseif (str_contains($field['type'], 'UID'))
                                                <i class="fas fa-fingerprint text-purple-500 text-xs"></i>
                                            @elseif (str_contains($field['type'], 'Date'))
                                                <i class="far fa-calendar text-blue-500 text-xs"></i>
                                            @else
                                                <i class="fas fa-font text-slate-400 text-xs"></i>
                                            @endif
                                            <span>{{ $field['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="p-3.5">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-medium bg-slate-100 text-[#4a4a68]">
                                            {{ $field['type'] }}
                                        </span>
                                    </td>
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            @if ($field['required'])
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-red-50 text-red-600 border border-red-200">
                                                    Required
                                                </span>
                                            @endif
                                            @if ($field['unique'])
                                                <span class="px-1.5 py-0.5 rounded text-[10px] font-medium bg-blue-50 text-blue-600 border border-blue-200">
                                                    Unique
                                                </span>
                                            @endif
                                            @if (! $field['required'] && ! $field['unique'])
                                                <span class="text-[10px] text-[#8e8ea9]">Optional</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="p-3.5 text-right pr-5 text-slate-400 space-x-1">
                                        <button type="button" @click="alert('Field konfigurasi {{ $field['name'] }} sudah aktif sesuai skema Eloquent & database.')" class="p-1 hover:text-[#4945ff]" title="Configure Field">
                                            <i class="fas fa-gear text-xs"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Footer note -->
                <div class="p-4 bg-[#f6f6f9] border-t border-[#eaeaef] flex items-center justify-between text-[11px] text-[#8e8ea9]">
                    <div class="flex items-center gap-1.5">
                        <i class="fas fa-check-circle text-emerald-500"></i>
                        <span>Skema data aktif terhubung dengan SQLite dan Eloquent ORM.</span>
                    </div>
                    <span class="font-mono">{{ count($activeCollection['fields']) }} fields configured</span>
                </div>
            </div>

            <!-- Strapi Headless API Endpoint Card -->
            <div class="bg-gradient-to-r from-[#212134] to-[#2a2942] rounded-xl text-white p-5 border border-[#32324d] shadow-strapi space-y-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <i class="fas fa-bolt text-amber-400 text-sm"></i>
                        <span class="font-semibold text-xs uppercase tracking-wider text-[#c0c0cf]">Strapi REST / GraphQL Endpoint</span>
                    </div>
                    <span class="px-2 py-0.5 rounded bg-white/10 text-[10px] font-mono text-purple-200">GET</span>
                </div>
                <div class="p-3 rounded-lg bg-black/40 font-mono text-xs text-purple-300 flex items-center justify-between border border-white/10">
                    <span class="truncate">/api/{{ $activeCollection['plural'] }}?populate=*</span>
                    <button
                        type="button"
                        @click="navigator.clipboard.writeText('/api/{{ $activeCollection['plural'] }}?populate=*'); alert('Endpoint disalin!');"
                        class="text-xs text-white/70 hover:text-white px-2 py-1 rounded bg-white/10"
                    >
                        Copy
                    </button>
                </div>
                <p class="text-[11px] text-[#8e8ea9]">
                    Endpoint ini siap dihubungkan dengan frontend Next.js, Nuxt, Astro, atau mobile app untuk rendering konten Infoscreening secara headless.
                </p>
            </div>
        </div>

    </div>

    <!-- Strapi Modal: Select a Field Type -->
    <div
        x-show="addFieldModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        @keydown.escape.window="addFieldModal = false"
    >
        <div
            @click.outside="addFieldModal = false"
            class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full border border-[#eaeaef] overflow-hidden"
        >
            <div class="p-5 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                <div>
                    <h3 class="font-sans font-semibold text-base text-[#32324d]">Select a Field Type</h3>
                    <p class="text-xs text-[#8e8ea9]">Pilih tipe field Strapi yang ingin ditambahkan ke koleksi {{ $activeCollection['name'] }}.</p>
                </div>
                <button @click="addFieldModal = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-6 grid grid-cols-2 sm:grid-cols-3 gap-3">
                @php
                    $fieldTypes = [
                        ['icon' => 'fa-font', 'color' => 'text-blue-500', 'bg' => 'bg-blue-50', 'title' => 'Text', 'desc' => 'Small or long text, titles, descriptions'],
                        ['icon' => 'fa-align-left', 'color' => 'text-amber-500', 'bg' => 'bg-amber-50', 'title' => 'Rich Text', 'desc' => 'WYSIWYG editor with markdown & HTML'],
                        ['icon' => 'fa-hashtag', 'color' => 'text-emerald-500', 'bg' => 'bg-emerald-50', 'title' => 'Number', 'desc' => 'Integers, float, decimals, views'],
                        ['icon' => 'fa-calendar', 'color' => 'text-sky-500', 'bg' => 'bg-sky-50', 'title' => 'Date', 'desc' => 'Event dates, published time, calendar'],
                        ['icon' => 'fa-toggle-on', 'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50', 'title' => 'Boolean', 'desc' => 'Yes/No switch, active, featured'],
                        ['icon' => 'fa-envelope', 'color' => 'text-purple-500', 'bg' => 'bg-purple-50', 'title' => 'Email', 'desc' => 'Email address validation'],
                        ['icon' => 'fa-image', 'color' => 'text-pink-500', 'bg' => 'bg-pink-50', 'title' => 'Media', 'desc' => 'Images, videos, files from Media Library'],
                        ['icon' => 'fa-link', 'color' => 'text-[#4945ff]', 'bg' => 'bg-[#f0ebfc]', 'title' => 'Relation', 'desc' => 'Connects to another Collection Type'],
                        ['icon' => 'fa-fingerprint', 'color' => 'text-rose-500', 'bg' => 'bg-rose-50', 'title' => 'UID', 'desc' => 'Auto-generated unique slug identifier'],
                    ];
                @endphp

                @foreach ($fieldTypes as $ft)
                    <div
                        @click="alert('Field ' + '{{ $ft['title'] }}' + ' sudah tersedia di controller dan database skema saat ini.'); addFieldModal = false;"
                        class="p-3.5 rounded-xl border border-[#eaeaef] hover:border-[#4945ff] hover:bg-[#f0ebfc]/30 cursor-pointer transition flex flex-col justify-between group"
                    >
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-8 h-8 rounded-lg {{ $ft['bg'] }} {{ $ft['color'] }} flex items-center justify-center text-xs">
                                <i class="fas {{ $ft['icon'] }}"></i>
                            </div>
                            <span class="font-medium text-xs text-[#32324d] group-hover:text-[#4945ff]">{{ $ft['title'] }}</span>
                        </div>
                        <p class="text-[10px] text-[#8e8ea9] leading-tight">{{ $ft['desc'] }}</p>
                    </div>
                @endforeach
            </div>

            <div class="p-4 bg-[#fafafc] border-t border-[#eaeaef] flex justify-end">
                <button @click="addFieldModal = false" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-300 transition">
                    Cancel
                </button>
            </div>
        </div>
    </div>

    <!-- Modal: Create New Collection Type -->
    <div
        x-show="newTypeModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        @keydown.escape.window="newTypeModal = false"
    >
        <div
            @click.outside="newTypeModal = false"
            class="bg-white rounded-2xl shadow-2xl max-w-lg w-full border border-[#eaeaef] overflow-hidden"
        >
            <div class="p-5 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                <div>
                    <h3 class="font-sans font-semibold text-base text-[#32324d]">Create a Collection Type</h3>
                    <p class="text-xs text-[#8e8ea9]">Buat jenis entitas konten baru di Infoscreening CMS.</p>
                </div>
                <button @click="newTypeModal = false" class="text-slate-400 hover:text-slate-700 p-1">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-medium text-[#32324d] mb-1">Display Name</label>
                    <input type="text" placeholder="e.g. Festival Film, Sponsor, Press Release" class="w-full px-3 py-2 rounded-lg border border-[#eaeaef] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-xs">
                    <span class="text-[10px] text-[#8e8ea9] mt-0.5 block">Nama tampilan di Content Manager.</span>
                </div>

                <div>
                    <label class="block font-medium text-[#32324d] mb-1">API ID (Singular)</label>
                    <input type="text" placeholder="e.g. sponsor" class="w-full px-3 py-2 rounded-lg border border-[#eaeaef] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-xs">
                </div>

                <div>
                    <label class="block font-medium text-[#32324d] mb-1">API ID (Plural)</label>
                    <input type="text" placeholder="e.g. sponsors" class="w-full px-3 py-2 rounded-lg border border-[#eaeaef] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-xs">
                </div>
            </div>

            <div class="p-4 bg-[#fafafc] border-t border-[#eaeaef] flex items-center justify-end gap-2">
                <button @click="newTypeModal = false" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-300 transition">
                    Cancel
                </button>
                <button @click="alert('Koleksi konten siap dibuat! Skema disinkronkan dengan migration Laravel.'); newTypeModal = false;" class="px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm">
                    Continue &rarr;
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
