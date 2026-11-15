<x-guest-layout>
    <h1 class="text-lg font-semibold text-slate-900 mb-1">Atur Ulang Password</h1>
    <div class="mb-4 text-sm text-slate-600">
        Buat password baru untuk akun Anda. Gunakan kombinasi yang kuat dan mudah Anda ingat.
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700" role="alert">
            <p class="font-semibold mb-1">Password gagal diatur ulang:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div>
            <x-input-label for="email" value="Email" />
            <x-text-input
                id="email"
                class="mt-1 block w-full {{ $errors->has('email') ? 'border-red-500 focus:border-red-600 focus:ring-red-500/20' : '' }}"
                type="email"
                name="email"
                :value="old('email', $request->email)"
                required
                autofocus
                autocomplete="username"
                aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                aria-describedby="email-error"
            />
            <x-input-error id="email-error" :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" value="Password baru" />
            <x-password-input
                id="password"
                class="mt-1"
                name="password"
                :hasError="$errors->has('password')"
                required
                autocomplete="new-password"
                aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                aria-describedby="password-error"
            />
            <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
            <x-input-error id="password-error" :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password_confirmation" value="Konfirmasi password baru" />
            <x-password-input
                id="password_confirmation"
                class="mt-1"
                name="password_confirmation"
                :hasError="$errors->has('password_confirmation') || $errors->has('password')"
                required
                autocomplete="new-password"
                aria-invalid="{{ $errors->has('password_confirmation') ? 'true' : 'false' }}"
                aria-describedby="password-confirmation-error"
            />
            <x-input-error id="password-confirmation-error" :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="mt-6 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-3">
            <a href="{{ route('login') }}" class="text-sm text-[#3A07BB] underline hover:text-[#24047a] focus:outline-none focus-visible:ring-2 focus-visible:ring-[#3A07BB] rounded">Kembali ke login</a>
            <x-primary-button class="w-full sm:w-auto">Simpan password baru</x-primary-button>
        </div>
    </form>
</x-guest-layout>
