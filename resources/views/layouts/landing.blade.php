<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'CryptoRank SAW — Sistem Pendukung Keputusan Cryptocurrency')</title>
    <meta name="description" content="Sistem pendukung keputusan pemilihan cryptocurrency menggunakan metode Simple Additive Weighting dengan ranking transparan dan terukur.">
    <meta name="theme-color" content="#020617">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
</head>
<body class="min-h-screen bg-slate-950 font-sans text-slate-100 antialiased selection:bg-cyan-400 selection:text-slate-950">
    @yield('content')
    @stack('scripts')
</body>
</html>
