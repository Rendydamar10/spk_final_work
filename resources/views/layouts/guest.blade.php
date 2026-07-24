<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'CryptoRank SAW') }}</title>
    <meta name="theme-color" content="#020617">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-slate-900 antialiased">
    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-10 sm:px-6">
        <div aria-hidden="true" class="pointer-events-none absolute inset-0">
            <div class="crypto-grid absolute inset-0 opacity-35"></div>
            <div class="absolute -left-32 top-10 h-80 w-80 rounded-full bg-cyan-500/15 blur-3xl"></div>
            <div class="absolute -right-32 bottom-10 h-96 w-96 rounded-full bg-indigo-500/15 blur-3xl"></div>
        </div>

        <div class="relative z-10 w-full max-w-md">
            <a href="{{ route('home') }}" class="mx-auto flex w-fit items-center gap-3" aria-label="Kembali ke beranda CryptoRank">
                <span class="grid h-12 w-12 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 via-blue-500 to-violet-600 text-white shadow-lg shadow-cyan-500/20">
                    <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 16.5 9 12l3 3 7-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 7h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-lg font-extrabold tracking-tight text-white">CryptoRank</span>
                    <span class="block text-[10px] font-semibold uppercase tracking-[0.22em] text-cyan-300">SAW Decision System</span>
                </span>
            </a>

            <div class="mt-8 overflow-hidden rounded-[1.75rem] border border-white/10 bg-white p-6 shadow-2xl shadow-black/40 sm:p-8">
                {{ $slot }}
            </div>

            <div class="mt-6 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 transition hover:text-cyan-300">
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m12.5 5-5 5 5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                    Kembali ke halaman utama
                </a>
            </div>
        </div>
    </main>
</body>
</html>
