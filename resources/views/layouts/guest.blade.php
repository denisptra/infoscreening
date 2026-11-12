<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Infoscreening') }}</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('favicon.png') }}">
        <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
        <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('apple-touch-icon.png') }}">

        <link
            rel="preload"
            as="font"
            type="font/woff2"
            href="{{ asset('fonts/filament/filament/inter/inter-latin-wght-normal-DIHYUR35.woff2') }}"
            crossorigin
        >

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-900 antialiased bg-[#f8fafc]">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 px-4">
            <a href="{{ route('home') }}" class="mb-6">
                <x-application-logo variant="purple" class="h-10 sm:h-12 w-auto" />
            </a>

            <div class="w-full sm:max-w-md px-6 py-6 bg-white border border-slate-200/80 shadow-card overflow-hidden sm:rounded-2xl">
                {{ $slot }}
            </div>

            <p class="mt-6 text-[11px] text-slate-400">&copy; {{ date('Y') }} Infoscreening. Festival Film dan Rupa-Rupa Sinema.</p>
        </div>
    </body>
</html>
