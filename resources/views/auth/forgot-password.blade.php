<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Lupa password? Masukkan email akun Anda. Kami akan mengirimkan tautan untuk membuat password baru.') }}
    </div>

    @if (config('mail.default') === 'log')
        <div class="mb-4 rounded-lg bg-amber-50 p-3 text-sm text-amber-800" role="status">
            Pengiriman email belum aktif. Super Admin perlu mengatur SMTP pada environment aplikasi.
        </div>
    @endif

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4 flex items-center justify-between">
            <a class="text-sm text-gray-600 underline hover:text-gray-900" href="{{ route('login') }}">
                {{ __('Kembali ke halaman login') }}
            </a>

            <x-primary-button>
                {{ __('Kirim tautan reset') }}
            </x-primary-button>
        </div>

        <p class="mt-4 text-xs text-gray-500">
            {{ __('Tautan berlaku selama 60 menit. Jika email tidak masuk, cek folder spam atau hubungi Super Admin untuk memastikan mailbox aktif.') }}
        </p>
    </form>
</x-guest-layout>
