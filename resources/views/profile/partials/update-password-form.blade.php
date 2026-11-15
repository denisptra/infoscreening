<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            Ubah Password
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            Pastikan akun Anda menggunakan password yang kuat (minimal 8 karakter) agar tetap aman.
        </p>
    </header>

    @if ($errors->updatePassword->any())
        <div class="mt-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700" role="alert">
            <p class="font-semibold mb-1">Password gagal diperbarui:</p>
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->updatePassword->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <x-input-label for="update_password_current_password" value="Password Saat Ini" />
            <x-password-input
                id="update_password_current_password"
                name="current_password"
                class="mt-1"
                :hasError="$errors->updatePassword->has('current_password')"
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password" value="Password Baru" />
            <x-password-input
                id="update_password_password"
                name="password"
                class="mt-1"
                :hasError="$errors->updatePassword->has('password')"
                autocomplete="new-password"
            />
            <p class="mt-1 text-xs text-slate-500">Minimal 8 karakter.</p>
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="update_password_password_confirmation" value="Konfirmasi Password Baru" />
            <x-password-input
                id="update_password_password_confirmation"
                name="password_confirmation"
                class="mt-1"
                :hasError="$errors->updatePassword->has('password_confirmation') || $errors->updatePassword->has('password')"
                autocomplete="new-password"
            />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Simpan</x-primary-button>

            @if (session('status') === 'password-updated')
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 3000)"
                    class="text-sm font-medium text-emerald-700"
                >Password berhasil diperbarui.</p>
            @endif
        </div>
    </form>
</section>
