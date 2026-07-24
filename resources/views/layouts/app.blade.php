<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SPK Crypto SAW') }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-900">
<div class="min-h-screen flex">
    <aside class="w-72 bg-slate-950 text-white hidden md:flex md:flex-col">
        <div class="p-6 border-b border-slate-800">
            <div class="text-xl font-bold">SPK Crypto SAW</div>
            <div class="text-xs text-slate-400 mt-1">{{ auth()->user()->role === 'admin' ? 'Admin Panel' : 'User Panel' }}</div>
        </div>

        <nav class="flex-1 p-4 space-y-1 text-sm">
            <a href="{{ route('dashboard') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('dashboard') ? 'bg-indigo-600' : '' }}">Dashboard</a>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.crypto.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('admin.crypto.*') ? 'bg-indigo-600' : '' }}">Data Crypto</a>
                <a href="{{ route('admin.ranking.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('admin.ranking.*') ? 'bg-indigo-600' : '' }}">Ranking Global</a>
                <a href="{{ route('admin.criteria.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('admin.criteria.*') ? 'bg-indigo-600' : '' }}">Kelola Kriteria</a>
                <a href="{{ route('admin.refresh.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('admin.refresh.*') ? 'bg-indigo-600' : '' }}">Refresh Coin</a>
                <a href="{{ route('admin.logs.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('admin.logs.*') ? 'bg-indigo-600' : '' }}">Log API</a>
                <a href="{{ route('admin.reports.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('admin.reports.*') ? 'bg-indigo-600' : '' }}">Laporan</a>
            @else
                <a href="{{ route('user.ranking.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('user.ranking.*') ? 'bg-indigo-600' : '' }}">Ranking Saya</a>
                <a href="{{ route('user.watchlist.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('user.watchlist.*') ? 'bg-indigo-600' : '' }}">Watchlist</a>
                <a href="{{ route('user.compare.index') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('user.compare.*') ? 'bg-indigo-600' : '' }}">Perbandingan Coin</a>
                <a href="{{ route('user.saw.method') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800 {{ request()->routeIs('user.saw.method') ? 'bg-indigo-600' : '' }}">Metode SAW</a>
            @endif

            <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800">Profil</a>
        </nav>

        <div class="p-4 border-t border-slate-800">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="w-full px-4 py-3 bg-slate-800 hover:bg-slate-700 rounded-xl text-left">Logout</button>
            </form>
        </div>
    </aside>

    <main class="flex-1">
        <header class="bg-white border-b border-slate-200 px-4 md:px-8 py-4 flex items-center justify-between">
            <div>
                <div class="text-lg font-semibold">@yield('title', 'Dashboard')</div>
                <div class="text-xs text-slate-500">Login sebagai {{ auth()->user()->name }} - {{ strtoupper(auth()->user()->role) }}</div>
            </div>
        </header>

        <section class="p-4 md:p-8">
            @include('partials.flash')
            @yield('content')
        </section>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>
