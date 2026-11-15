<x-guest-layout>
    <h1 class="text-lg font-semibold text-slate-900 mb-1">Konfirmasi Password</h1>
    <div class="mb-4 text-sm text-slate-600">
        Ini adalah area aman aplikasi. Silakan konfirmasi password Anda sebelum melanjutkan.
    </div>

    @if ($errors->any())
        <div class="mb-4 rounded-lg border border-red-200 bg-red-50 p-3 text-xs text-red-700" role="alert">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <div>
            <x-input-label for="password" value="Password" />
            <x-password-input
                id="password"
                class="mt-1"
                name="password"
                :hasError="$errors->has('password')"
                required
                autocomplete="current-password"
            />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button>
                Konfirmasi
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
