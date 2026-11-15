@extends('layouts.admin')

@section('page_title', 'Settings')

@section('content')
<div class="space-y-6" x-data="{
    activeTab: @js($activeTab),
    tokenModal: false,
    tokenFormDirty: false,
    requestTokenModalClose() {
        if (! this.tokenFormDirty) {
            this.tokenModal = false;
            return;
        }

        window.dispatchEvent(new CustomEvent('app-confirm', {
            detail: {
                title: 'Tutup formulir token?',
                message: 'Data token yang belum dibuat akan hilang.',
                action: 'Buang perubahan',
                onConfirm: () => {
                    this.tokenModal = false;
                    this.tokenFormDirty = false;
                },
            },
        }));
    },
}">

    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[11px] font-medium bg-[#f0ebfc] text-[#4945ff] mb-1">
                <i class="fas fa-gear text-[10px]"></i>
                <span>General / Settings</span>
            </div>
            <h1 class="font-sans font-semibold text-xl text-[#32324d] tracking-tight">Strapi CMS Settings</h1>
            <p class="text-xs text-[#8e8ea9] font-normal">Kelola akses tim redaksi dan integrasi situs.</p>
        </div>

        <div class="flex items-center gap-2">
            <form method="POST" action="{{ route('strapi.settings.clear-cache') }}" class="inline">
                @csrf
                <button
                    type="submit"
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-white hover:bg-gray-50 text-gray-700 text-xs font-medium border border-gray-300 shadow-sm transition"
                    title="Bersihkan cache views, routes, dan konfigurasi"
                >
                    <i class="fas fa-broom text-[10px] text-gray-500"></i>
                    <span>Bersihkan Cache</span>
                </button>
            </form>
            <button
                @click="tokenFormDirty = false; tokenModal = true"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium shadow-sm transition"
            >
                <i class="fas fa-key text-[10px]"></i>
                <span>Generate API Token</span>
            </button>
        </div>
    </div>

    <!-- Alert Status if any -->
    @if (session('status'))
        <div class="p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-circle-check text-emerald-500"></i>
                <span>{{ session('status') }}</span>
            </div>
        </div>
    @endif

    <!-- Settings 2-Column Layout (Strapi style) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        <!-- Left Nav (3 cols) -->
        <div class="lg:col-span-3 space-y-4">
            <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi p-2 space-y-4 text-xs">

                <div>
                    <div class="px-3 py-1 text-[10px] font-semibold text-[#8e8ea9] uppercase tracking-wider">
                        Global Settings
                    </div>
                    <button
                        @click="activeTab = 'general'"
                        :class="activeTab === 'general' ? 'bg-[#f0ebfc] text-[#4945ff] font-medium' : 'text-[#32324d] hover:bg-[#f6f6f9]'"
                        class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-lg transition"
                    >
                        <i class="fas fa-sliders w-4 text-center"></i>
                        <span>Overview & System</span>
                    </button>
                </div>

                <div>
                    <div class="px-3 py-1 text-[10px] font-semibold text-[#8e8ea9] uppercase tracking-wider">
                        Administration Panel
                    </div>
                    <button
                        @click="activeTab = 'roles'"
                        :class="activeTab === 'roles' ? 'bg-[#f0ebfc] text-[#4945ff] font-medium' : 'text-[#32324d] hover:bg-[#f6f6f9]'"
                        class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-lg transition"
                    >
                        <i class="fas fa-shield-halved w-4 text-center"></i>
                        <span>Roles & Permissions</span>
                    </button>
                    <button
                        @click="activeTab = 'users'"
                        :class="activeTab === 'users' ? 'bg-[#f0ebfc] text-[#4945ff] font-medium' : 'text-[#32324d] hover:bg-[#f6f6f9]'"
                        class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-lg transition"
                    >
                        <i class="fas fa-users w-4 text-center"></i>
                        <span>Admin Users ({{ $users->count() }})</span>
                    </button>
                </div>

                <div>
                    <div class="px-3 py-1 text-[10px] font-semibold text-[#8e8ea9] uppercase tracking-wider">
                        API & Webhooks
                    </div>
                    <button
                        @click="activeTab = 'tokens'"
                        :class="activeTab === 'tokens' ? 'bg-[#f0ebfc] text-[#4945ff] font-medium' : 'text-[#32324d] hover:bg-[#f6f6f9]'"
                        class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-lg transition"
                    >
                        <i class="fas fa-key w-4 text-center"></i>
                        <span>API Tokens</span>
                    </button>
                    <button
                        @click="activeTab = 'webhooks'"
                        :class="activeTab === 'webhooks' ? 'bg-[#f0ebfc] text-[#4945ff] font-medium' : 'text-[#32324d] hover:bg-[#f6f6f9]'"
                        class="w-full text-left flex items-center gap-2.5 px-3 py-2 rounded-lg transition"
                    >
                        <i class="fas fa-bolt w-4 text-center"></i>
                        <span>Webhooks</span>
                    </button>
                </div>

            </div>
        </div>

        <!-- Right Content Area (9 cols) -->
        <div class="lg:col-span-9 space-y-6">

            <!-- TAB 1: GENERAL OVERVIEW -->
            <div x-show="activeTab === 'general'" class="space-y-6">
                <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi p-6 space-y-6">
                    <div class="border-b border-[#eaeaef] pb-4">
                        <h3 class="font-sans font-semibold text-base text-[#32324d]">System Information</h3>
                        <p class="text-xs text-[#8e8ea9]">Spesifikasi arsitektur headless CMS dan lingkungan hosting.</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                        <div class="p-4 rounded-xl bg-[#fafafc] border border-[#eaeaef] space-y-1">
                            <span class="text-[#8e8ea9] text-[11px]">Strapi CMS Design Engine</span>
                            <div class="font-semibold text-sm text-[#32324d] flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-[#4945ff]"></span>
                                <span>v5.2.0 (Custom Laravel Hybrid)</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-[#fafafc] border border-[#eaeaef] space-y-1">
                            <span class="text-[#8e8ea9] text-[11px]">Database Engine</span>
                            <div class="font-semibold text-sm text-[#32324d] flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                                <span>{{ strtoupper($databaseDriver) }}</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-[#fafafc] border border-[#eaeaef] space-y-1">
                            <span class="text-[#8e8ea9] text-[11px]">Media Storage Driver</span>
                            <div class="font-semibold text-sm text-[#32324d] flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-500"></span>
                                <span>Local / Public Symlink Disk</span>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl bg-[#fafafc] border border-[#eaeaef] space-y-1">
                            <span class="text-[#8e8ea9] text-[11px]">Brand Identity Color</span>
                            <div class="font-semibold text-sm text-[#3A07BB] flex items-center gap-2">
                                <span class="w-4 h-4 rounded-full bg-[#3A07BB] border border-black/10"></span>
                                <span>#3A07BB (Infoscreening Purple)</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-[#eaeaef] pt-4 flex items-center justify-between text-xs text-[#8e8ea9]">
                        <span>Environment: <strong>{{ app()->environment() }}</strong></span>
                        <span>Debugging: <strong>{{ config('app.debug') ? 'Active' : 'Off' }}</strong></span>
                    </div>
                </div>
            </div>

            <!-- TAB 2: ROLES & PERMISSIONS -->
            <div x-show="activeTab === 'roles'" class="space-y-6" x-cloak>
                <div class="space-y-3">
                    <article class="rounded-xl border border-[#eaeaef] bg-white p-5">
                        <h3 class="text-sm font-semibold text-[#32324d]">Super Admin</h3>
                        <p class="mt-1 text-xs leading-relaxed text-[#666687]">Akses penuh ke seluruh konten, akun dan peran, pengaturan CMS, menu, dropdown, serta footer.</p>
                    </article>
                    <article class="rounded-xl border border-[#eaeaef] bg-white p-5">
                        <h3 class="text-sm font-semibold text-[#32324d]">Admin</h3>
                        <p class="mt-1 text-xs leading-relaxed text-[#666687]">Membuat, mengedit, dan menerbitkan artikel. Tidak dapat mengubah akun, menu, footer, kategori, event, atau pengaturan situs.</p>
                    </article>
                    <article class="rounded-xl border border-[#eaeaef] bg-white p-5">
                        <h3 class="text-sm font-semibold text-[#32324d]">Editor</h3>
                        <p class="mt-1 text-xs leading-relaxed text-[#666687]">Menulis dan mengedit draf miliknya sendiri, melihat hanya artikelnya sendiri, serta tidak dapat menerbitkan, menjadwalkan, atau memilih artikel unggulan.</p>
                    </article>
                    <article class="rounded-xl border border-[#eaeaef] bg-white p-5">
                        <h3 class="text-sm font-semibold text-[#32324d]">Pembaca</h3>
                        <p class="mt-1 text-xs leading-relaxed text-[#666687]">Menggunakan halaman publik dan mengirim kontribusi; tidak dapat masuk ke panel CMS.</p>
                    </article>
                </div>
            </div>

            <!-- TAB 3: ADMIN USERS -->
            <div x-show="activeTab === 'users'" class="space-y-6" x-cloak>
                <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                    <div class="p-5 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                        <div>
                            <h3 class="font-sans font-semibold text-base text-[#32324d]">Administrator Users</h3>
                            <p class="text-xs text-[#8e8ea9]">Atur siapa yang dapat mengelola konten dan situs. Hanya Super Admin yang dapat mengubah peran.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-[#eaeaef]">
                        @foreach ($users as $user)
                            <div class="p-4 flex items-center justify-between gap-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-[#3A07BB] text-white flex items-center justify-center font-semibold text-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="font-medium text-xs text-[#32324d]">{{ $user->name }}</div>
                                        <div class="text-[11px] text-[#8e8ea9]">{{ $user->email }}</div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('strapi.settings.users.role', $user) }}" class="flex items-center gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <select name="role" class="rounded-md border border-[#dcdce4] px-2 py-1 text-[11px] text-[#32324d]">
                                            @foreach ($roles as $role)
                                                <option value="{{ $role->value }}" @selected($user->role === $role)>{{ $role->label() }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="rounded-md bg-[#4945ff] px-3 py-1.5 text-[11px] font-medium text-white hover:bg-[#3b37eb]">
                                            Simpan
                                        </button>
                                    </form>
                                </div>
                                @error('role')
                                    <p class="mt-2 text-[11px] text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TAB 4: API TOKENS -->
            <div x-show="activeTab === 'tokens'" class="space-y-6" x-cloak>
                <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                    <div class="p-5 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                        <div>
                            <h3 class="font-sans font-semibold text-base text-[#32324d]">API Tokens</h3>
                            <p class="text-xs text-[#8e8ea9]">Token API otentikasi untuk REST API dan integrasi frontend headless.</p>
                        </div>
                        <button
                            @click="tokenFormDirty = false; tokenModal = true"
                            class="px-3 py-1.5 rounded-lg bg-[#4945ff] text-white text-xs font-medium hover:bg-[#3b37eb] transition"
                        >
                            <i class="fas fa-plus text-[10px] mr-1"></i> Add Token
                        </button>
                    </div>

                    <div class="divide-y divide-[#eaeaef]">
                        @foreach ($apiTokens as $tok)
                            <div class="p-4.5 space-y-2 hover:bg-[#fafafc] transition">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-xs text-[#32324d]">{{ $tok['name'] }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] uppercase font-mono {{ $tok['type'] === 'read-only' ? 'bg-blue-50 text-blue-600' : 'bg-purple-50 text-purple-600' }}">
                                            {{ $tok['type'] }}
                                        </span>
                                    </div>
                                    <span class="text-[11px] text-[#8e8ea9]">Expires: {{ $tok['expires_at'] }}</span>
                                </div>
                                <p class="text-xs text-[#8e8ea9]">{{ $tok['description'] }}</p>
                                <div class="flex items-center gap-2 pt-1">
                                    <div class="px-3 py-1 rounded bg-[#f6f6f9] border border-[#eaeaef] font-mono text-[11px] text-slate-700 select-all truncate max-w-md">
                                        {{ $tok['token'] }}
                                    </div>
                                    <button
                                        type="button"
                                        @click="$dispatch('app-copy', { text: @js($tok['token']), label: 'API Token' })"
                                        class="text-xs text-[#4945ff] hover:underline font-medium"
                                    >
                                        Copy
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- TAB 5: WEBHOOKS -->
            <div x-show="activeTab === 'webhooks'" class="space-y-6" x-cloak>
                <div class="bg-white rounded-xl border border-[#eaeaef] shadow-strapi overflow-hidden">
                    <div class="p-5 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                        <div>
                            <h3 class="font-sans font-semibold text-base text-[#32324d]">Webhooks</h3>
                            <p class="text-xs text-[#8e8ea9]">Contoh endpoint saja; pengiriman webhook belum dikonfigurasi.</p>
                        </div>
                    </div>

                    <div class="divide-y divide-[#eaeaef]">
                        @foreach ($webhooks as $wh)
                            <div class="p-4.5 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="font-medium text-xs text-[#32324d]">{{ $wh['name'] }}</span>
                                        <span class="inline-flex items-center gap-1 text-[10px] font-medium text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span> Belum terhubung
                                        </span>
                                    </div>
                                    <button
                                        type="button"
                                    disabled
                                    title="Pengiriman webhook belum tersedia"
                                    class="cursor-not-allowed rounded border border-[#eaeaef] px-2.5 py-1 text-[11px] font-medium text-[#8e8ea9]"
                                    >
                                    Uji belum tersedia
                                    </button>
                                </div>
                                <div class="font-mono text-[11px] text-[#8e8ea9] truncate">{{ $wh['url'] }}</div>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @foreach ($wh['events'] as $ev)
                                        <span class="px-2 py-0.5 rounded bg-slate-100 text-[10px] font-mono text-slate-600">
                                            {{ $ev }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>

    </div>

    <!-- Modal Generate API Token -->
    <div
        x-show="tokenModal"
        x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-xs"
        @click.self="requestTokenModalClose()"
        @keydown.escape.window="requestTokenModalClose()"
    >
        <section
            role="dialog"
            aria-modal="true"
            aria-labelledby="create-token-title"
            class="bg-white rounded-2xl shadow-2xl max-w-md w-full border border-[#eaeaef] overflow-hidden"
        >
            <form method="POST" action="{{ route('strapi.settings.tokens.create') }}" @input="tokenFormDirty = true">
                @csrf
                <div class="p-5 border-b border-[#eaeaef] flex items-center justify-between bg-[#fafafc]">
                    <div>
                        <h3 id="create-token-title" class="font-sans font-semibold text-base text-[#32324d]">Create API Token</h3>
                        <p class="text-xs text-[#8e8ea9]">Generate token baru untuk akses Strapi API.</p>
                    </div>
                    <button type="button" @click="requestTokenModalClose()" aria-label="Tutup formulir token" class="text-slate-400 hover:text-slate-700 p-1">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>

                <div class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-medium text-[#32324d] mb-1">Token Name</label>
                        <input type="text" name="name" required placeholder="e.g. Next.js Frontend Production" class="w-full px-3 py-2 rounded-lg border border-[#eaeaef] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-xs">
                    </div>

                    <div>
                        <label class="block font-medium text-[#32324d] mb-1">Token Type</label>
                        <select name="type" required class="w-full px-3 py-2 rounded-lg border border-[#eaeaef] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-xs">
                            <option value="read-only">Read-Only (GET requests only)</option>
                            <option value="full-access">Full-Access (GET, POST, PUT, DELETE)</option>
                            <option value="custom">Custom Permissions</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-medium text-[#32324d] mb-1">Description</label>
                        <textarea name="description" rows="2" placeholder="Deskripsi peruntukan token..." class="w-full px-3 py-2 rounded-lg border border-[#eaeaef] focus:border-[#4945ff] focus:ring-1 focus:ring-[#4945ff] text-xs"></textarea>
                    </div>
                </div>

                <div class="p-4 bg-[#fafafc] border-t border-[#eaeaef] flex items-center justify-end gap-2">
                    <button type="button" @click="requestTokenModalClose()" class="px-4 py-2 rounded-lg bg-slate-200 text-slate-700 text-xs font-medium hover:bg-slate-300 transition">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-lg bg-[#4945ff] hover:bg-[#3b37eb] text-white text-xs font-medium transition shadow-sm">
                        Create Token &rarr;
                    </button>
                </section>
            </form>
        </div>
    </div>

</div>
@endsection
