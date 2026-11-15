<x-guest-layout>
    <h1 class="text-lg font-semibold text-slate-900 mb-1">Daftar Akun Baru</h1>
    <p class="mb-4 text-sm text-slate-600">Lengkapi data di bawah ini untuk membuat akun Infoscreening.</p>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700" role="alert">
            <p class="font-semibold mb-1">Pendaftaran belum berhasil:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input
                id="name"
                class="block mt-1 w-full {{ $errors->has('name') ? 'border-red-500 focus:border-red-600 focus:ring-red-500/20' : '' }}"
                type="text"
                name="name"
                :value="old('name')"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="block mt-1 w-full {{ $errors->has('email') ? 'border-red-500 focus:border-red-600 focus:ring-red-500/20' : '' }}"
                type="email"
                name="email"
                :value="old('email')"
                required
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
                :hasError="$errors->has('password')"
                required
                autocomplete="new-password"
            />
            <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi Password" />
            <x-password-input
                id="password_confirmation"
                class="mt-1"
                name="password_confirmation"
                :hasError="$errors->has('password_confirmation') || $errors->has('password')"
                required
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3 mt-5">
            <a class="underline text-sm text-[#3A07BB] hover:text-[#24047a] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#3A07BB]" href="{{ route('login') }}">
                Sudah punya akun? Masuk
            </a>

            <x-primary-button class="w-full sm:w-auto">
                Daftar
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
