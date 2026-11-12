@php
    $roleAccess = [
        'super_admin' => [
            'label' => 'Super Admin',
            'summary' => 'Akses penuh ke seluruh sistem.',
            'items' => [
                'Buat, edit, terbitkan, dan hapus semua artikel',
                'Kelola kategori, tag, event, expert, dan produk',
                'Kelola media, kotak masuk kontribusi, navigasi, dan footer',
                'Pengaturan situs, API token, dan ubah peran pengguna',
            ],
        ],
        'admin' => [
            'label' => 'Admin / Redaksi',
            'summary' => 'Mengelola konten redaksi sehari-hari.',
            'items' => [
                'Buat, edit, terbitkan, dan hapus semua artikel',
                'Kelola kategori, tag, event, expert, dan produk',
                'Kelola media dan kotak masuk kontribusi',
                'Atur navigasi dan tampilan footer',
            ],
        ],
        'editor' => [
            'label' => 'Editor',
            'summary' => 'Menulis dan mengelola draf milik sendiri.',
            'items' => [
                'Buat dan edit artikel milik sendiri',
                'Simpan sebagai draf untuk ditinjau Admin',
                'Unggah dan pilih gambar dari Media Library',
            ],
        ],
    ];

    $currentRole = auth()->user()->role?->value ?? 'member';
@endphp

<section class="rounded-xl border border-[#eaeaef] bg-white p-5">
    <div class="flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h2 class="text-sm font-semibold text-[#32324d]">Peran &amp; Hak Akses</h2>
            <p class="mt-1 text-xs text-[#8e8ea9]">Ringkasan sederhana siapa bisa melakukan apa di panel ini.</p>
        </div>
        <span class="text-[11px] text-[#666687]">
            Peran Anda: <strong class="text-[#32324d]">{{ auth()->user()->role?->label() ?? 'Pembaca' }}</strong>
        </span>
    </div>

    <div class="mt-4 grid grid-cols-1 gap-4 md:grid-cols-3">
        @foreach ($roleAccess as $roleKey => $access)
            @php($isCurrent = $currentRole === $roleKey)
            <div class="rounded-lg border p-4 {{ $isCurrent ? 'border-[#4945ff] bg-[#f5f3ff]' : 'border-[#eaeaef] bg-[#fafafc]' }}">
                <div class="flex items-center justify-between gap-2">
                    <h3 class="text-xs font-semibold text-[#32324d]">{{ $access['label'] }}</h3>
                    @if ($isCurrent)
                        <span class="rounded-full bg-[#4945ff] px-2 py-0.5 text-[9px] font-medium uppercase tracking-wide text-white">Anda</span>
                    @endif
                </div>
                <p class="mt-1 text-[11px] text-[#666687]">{{ $access['summary'] }}</p>
                <ul class="mt-3 space-y-1.5">
                    @foreach ($access['items'] as $item)
                        <li class="flex items-start gap-2 text-[11px] leading-relaxed text-[#49445f]">
                            <i class="fas fa-check mt-0.5 text-[9px] text-emerald-500"></i>
                            <span>{{ $item }}</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>
