<x-guest-layout>
    <h1 class="text-lg font-semibold text-slate-900 mb-1">Masuk ke Akun</h1>
    <p class="mb-4 text-sm text-slate-600">Masukkan email dan password Anda untuk melanjutkan.</p>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700" role="alert">
            <p class="font-semibold mb-1">Gagal masuk:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="block mt-1 w-full {{ $errors->has('email') ? 'border-red-500 focus:border-red-600 focus:ring-red-500/20' : '' }}"
                type="email"
                name="email"
                :value="old('email')"
                required
                autofocus
                autocomplete="username"
            />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" value="Password" />
            <x-password-input
                id="password"
                class="mt-1"
                name="password"
                :hasError="$errors->has('password') || $errors->has('email')"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-[#3A07BB] shadow-sm focus:ring-[#3A07BB]" name="remember">
                <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="underline text-sm text-[#3A07BB] hover:text-[#24047a] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3A07BB]" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="flex items-center justify-between mt-5 gap-3">
            @if (Route::has('register'))
                <a class="underline text-sm text-slate-600 hover:text-[#3A07BB]" href="{{ route('register') }}">
                    Belum punya akun?
                </a>
            @endif

            <x-primary-button class="ms-auto">
                Masuk
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
