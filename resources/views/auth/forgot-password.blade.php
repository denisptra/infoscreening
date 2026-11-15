<x-guest-layout>
    <h1 class="text-lg font-semibold text-slate-900 mb-1">Lupa Password</h1>
    <div class="mb-4 text-sm text-slate-600">
        Lupa password? Masukkan email akun Anda. Jika email terdaftar, kami akan mengirimkan tautan untuk mengatur ulang password.
    </div>

    @if (config('mail.default') === 'log')
        <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 p-3 text-xs text-amber-800" role="status">
            Pengiriman email belum dikonfigurasi (MAIL_MAILER masih <code>log</code>). Minta Super Admin mengatur SMTP di environment aplikasi agar tautan reset dapat dikirim ke email.
        </div>
    @endif

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="mt-1 block w-full"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="email-error"
            />
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <a class="text-sm text-[#3A07BB] underline hover:text-[#24047a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3A07BB] rounded" href="{{ route('login') }}">
                Kembali ke login
            </a>

            <x-primary-button class="w-full sm:w-auto">
                Kirim tautan reset
            </x-primary-button>
        </div>

        <p class="mt-4 text-xs text-slate-600 leading-relaxed">
            Tautan reset berlaku selama {{ config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60) }} menit. Periksa juga folder spam/promosi jika email tidak terlihat.
        </p>
    </form>
</x-guest-layout>
