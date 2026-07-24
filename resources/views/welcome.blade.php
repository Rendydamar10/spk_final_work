@extends('layouts.landing')

@section('title', 'CryptoRank SAW — Ranking Cryptocurrency Lebih Terukur')

@section('content')
<div
    x-data="{ mobileMenuOpen: false }"
    class="relative min-h-screen overflow-hidden bg-slate-950"
>
    <div aria-hidden="true" class="pointer-events-none absolute inset-0">
        <div class="crypto-grid absolute inset-0 opacity-40"></div>
        <div class="absolute -left-40 top-20 h-96 w-96 rounded-full bg-cyan-500/15 blur-3xl"></div>
        <div class="absolute -right-32 top-1/3 h-[28rem] w-[28rem] rounded-full bg-indigo-500/15 blur-3xl"></div>
        <div class="absolute bottom-0 left-1/3 h-80 w-80 rounded-full bg-violet-500/10 blur-3xl"></div>
    </div>

    <header class="fixed inset-x-0 top-0 z-50 border-b border-white/5 bg-slate-950/75 backdrop-blur-xl">
        <nav class="mx-auto flex h-20 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ route('home') }}" class="group flex items-center gap-3" aria-label="CryptoRank SAW beranda">
                <span class="relative grid h-11 w-11 place-items-center overflow-hidden rounded-2xl border border-cyan-300/20 bg-gradient-to-br from-cyan-400 via-blue-500 to-violet-600 shadow-lg shadow-cyan-500/20">
                    <svg class="h-6 w-6 text-white transition-transform duration-300 group-hover:scale-110" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M5 16.5 9 12l3 3 7-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M14 7h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <span>
                    <span class="block text-base font-extrabold tracking-tight text-white">CryptoRank</span>
                    <span class="block text-[10px] font-semibold uppercase tracking-[0.24em] text-cyan-300">SAW Decision System</span>
                </span>
            </a>

            <div class="hidden items-center gap-8 lg:flex">
                <a href="#fitur" class="text-sm font-medium text-slate-300 transition hover:text-white">Fitur</a>
                <a href="#metode" class="text-sm font-medium text-slate-300 transition hover:text-white">Metode SAW</a>
                <a href="#kriteria" class="text-sm font-medium text-slate-300 transition hover:text-white">Kriteria</a>
                <a href="#tentang" class="text-sm font-medium text-slate-300 transition hover:text-white">Tentang</a>
            </div>

            <div class="hidden items-center gap-3 lg:flex">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-5 py-2.5 text-sm font-bold text-slate-950 transition hover:bg-cyan-100 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                        Buka Dashboard
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-4 py-2.5 text-sm font-semibold text-slate-200 transition hover:bg-white/5 hover:text-white">Masuk</a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-cyan-400 to-blue-500 px-5 py-2.5 text-sm font-bold text-slate-950 shadow-lg shadow-cyan-500/20 transition hover:-translate-y-0.5 hover:shadow-cyan-500/30 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                        Mulai Sekarang
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endauth
            </div>

            <button
                type="button"
                class="grid h-11 w-11 place-items-center rounded-xl border border-white/10 bg-white/5 text-slate-100 lg:hidden"
                @click="mobileMenuOpen = !mobileMenuOpen"
                :aria-expanded="mobileMenuOpen.toString()"
                aria-controls="mobile-navigation"
                aria-label="Buka menu navigasi"
            >
                <svg x-show="!mobileMenuOpen" class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
                <svg x-show="mobileMenuOpen" x-cloak class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m6 6 12 12M18 6 6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
        </nav>

        <div
            id="mobile-navigation"
            x-show="mobileMenuOpen"
            x-cloak
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-2"
            class="border-t border-white/5 bg-slate-950/95 px-4 pb-5 pt-3 backdrop-blur-xl lg:hidden"
        >
            <div class="mx-auto grid max-w-7xl gap-1">
                <a href="#fitur" @click="mobileMenuOpen = false" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-200 hover:bg-white/5">Fitur</a>
                <a href="#metode" @click="mobileMenuOpen = false" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-200 hover:bg-white/5">Metode SAW</a>
                <a href="#kriteria" @click="mobileMenuOpen = false" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-200 hover:bg-white/5">Kriteria</a>
                <a href="#tentang" @click="mobileMenuOpen = false" class="rounded-xl px-4 py-3 text-sm font-medium text-slate-200 hover:bg-white/5">Tentang</a>
                <div class="mt-3 grid grid-cols-2 gap-3 border-t border-white/5 pt-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="col-span-2 rounded-xl bg-white px-4 py-3 text-center text-sm font-bold text-slate-950">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl border border-white/10 px-4 py-3 text-center text-sm font-semibold text-white">Masuk</a>
                        <a href="{{ route('register') }}" class="rounded-xl bg-cyan-400 px-4 py-3 text-center text-sm font-bold text-slate-950">Daftar</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main class="relative z-10">
        <section class="px-4 pb-20 pt-32 sm:px-6 sm:pt-40 lg:px-8 lg:pb-28">
            <div class="mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-[1.02fr_.98fr] lg:gap-12">
                <div class="reveal-up">
                    <div class="inline-flex items-center gap-2 rounded-full border border-cyan-300/20 bg-cyan-300/5 px-3.5 py-2 text-xs font-semibold text-cyan-200 shadow-sm shadow-cyan-500/10">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-cyan-300 opacity-60"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-cyan-300"></span>
                        </span>
                        Sistem Pendukung Keputusan Cryptocurrency
                    </div>

                    <h1 class="mt-7 max-w-3xl text-4xl font-extrabold leading-[1.08] tracking-tight text-white sm:text-5xl lg:text-6xl xl:text-7xl">
                        Pilih crypto dengan
                        <span class="crypto-gradient-text">data, bukan asumsi.</span>
                    </h1>

                    <p class="mt-6 max-w-2xl text-base leading-8 text-slate-300 sm:text-lg">
                        CryptoRank membantu membandingkan cryptocurrency menggunakan metode
                        <strong class="font-semibold text-white">Simple Additive Weighting</strong>.
                        Setiap ranking dihitung dari kriteria pasar, momentum, dan risiko yang transparan.
                    </p>

                    <div class="mt-9 flex flex-col gap-3 sm:flex-row">
                        @auth
                            <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-6 py-4 text-sm font-extrabold text-slate-950 shadow-xl shadow-cyan-500/20 transition duration-300 hover:-translate-y-1 hover:shadow-cyan-500/30 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                                Buka Dashboard
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-cyan-400 to-blue-500 px-6 py-4 text-sm font-extrabold text-slate-950 shadow-xl shadow-cyan-500/20 transition duration-300 hover:-translate-y-1 hover:shadow-cyan-500/30 focus:outline-none focus:ring-2 focus:ring-cyan-300 focus:ring-offset-2 focus:ring-offset-slate-950">
                                Buat Ranking Saya
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </a>
                            <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl border border-white/10 bg-white/5 px-6 py-4 text-sm font-bold text-white backdrop-blur transition duration-300 hover:border-white/20 hover:bg-white/10">
                                Sudah punya akun
                            </a>
                        @endauth
                    </div>

                    <div class="mt-10 grid max-w-xl grid-cols-3 gap-3 sm:gap-5">
                        <div>
                            <div class="text-2xl font-extrabold text-white sm:text-3xl">6</div>
                            <div class="mt-1 text-xs leading-5 text-slate-400 sm:text-sm">Kriteria terukur</div>
                        </div>
                        <div class="border-l border-white/10 pl-4 sm:pl-6">
                            <div class="text-2xl font-extrabold text-white sm:text-3xl">100%</div>
                            <div class="mt-1 text-xs leading-5 text-slate-400 sm:text-sm">Bobot transparan</div>
                        </div>
                        <div class="border-l border-white/10 pl-4 sm:pl-6">
                            <div class="text-2xl font-extrabold text-white sm:text-3xl">SAW</div>
                            <div class="mt-1 text-xs leading-5 text-slate-400 sm:text-sm">Metode keputusan</div>
                        </div>
                    </div>
                </div>

                <div class="relative mx-auto w-full max-w-xl lg:max-w-none reveal-up reveal-delay-2">
                    <div aria-hidden="true" class="absolute -inset-8 rounded-full bg-gradient-to-br from-cyan-500/15 via-blue-500/10 to-violet-500/15 blur-3xl"></div>

                    <div class="relative animate-float-soft rounded-[2rem] border border-white/10 bg-slate-900/80 p-3 shadow-2xl shadow-black/40 backdrop-blur-xl sm:p-4">
                        <div class="rounded-[1.5rem] border border-white/5 bg-slate-950/75 p-4 sm:p-6">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-[0.2em] text-cyan-300">Ranking Global</div>
                                    <div class="mt-1 text-lg font-bold text-white">Top Cryptocurrency</div>
                                </div>
                                <div class="rounded-xl border border-emerald-400/20 bg-emerald-400/10 px-3 py-2 text-xs font-semibold text-emerald-300">
                                    Data aktif
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-3 gap-3">
                                <div class="rounded-2xl border border-white/5 bg-white/[0.035] p-3">
                                    <div class="text-[10px] uppercase tracking-wider text-slate-500">Alternatif</div>
                                    <div class="mt-1 text-lg font-bold text-white">10 Coin</div>
                                </div>
                                <div class="rounded-2xl border border-white/5 bg-white/[0.035] p-3">
                                    <div class="text-[10px] uppercase tracking-wider text-slate-500">Kriteria</div>
                                    <div class="mt-1 text-lg font-bold text-white">6 Aktif</div>
                                </div>
                                <div class="rounded-2xl border border-white/5 bg-white/[0.035] p-3">
                                    <div class="text-[10px] uppercase tracking-wider text-slate-500">Metode</div>
                                    <div class="mt-1 text-lg font-bold text-white">SAW</div>
                                </div>
                            </div>

                            <div class="mt-5 space-y-2.5">
                                @php
                                    $previewCoins = [
                                        ['rank' => 1, 'symbol' => 'BTC', 'name' => 'Bitcoin', 'score' => '0.9124', 'change' => '+2.81%', 'tone' => 'amber'],
                                        ['rank' => 2, 'symbol' => 'ETH', 'name' => 'Ethereum', 'score' => '0.8476', 'change' => '+1.94%', 'tone' => 'indigo'],
                                        ['rank' => 3, 'symbol' => 'SOL', 'name' => 'Solana', 'score' => '0.7651', 'change' => '+4.12%', 'tone' => 'violet'],
                                        ['rank' => 4, 'symbol' => 'BNB', 'name' => 'BNB', 'score' => '0.7018', 'change' => '-0.36%', 'tone' => 'yellow'],
                                    ];
                                @endphp

                                @foreach ($previewCoins as $coin)
                                    <div class="group grid grid-cols-[auto_1fr_auto] items-center gap-3 rounded-2xl border border-white/5 bg-white/[0.035] p-3 transition duration-300 hover:border-cyan-300/15 hover:bg-white/[0.06] sm:p-4">
                                        <div class="grid h-8 w-8 place-items-center rounded-xl bg-white/5 text-xs font-extrabold text-slate-300">#{{ $coin['rank'] }}</div>
                                        <div class="flex min-w-0 items-center gap-3">
                                            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-full border border-white/10 bg-gradient-to-br from-slate-700 to-slate-900 text-xs font-extrabold text-white shadow-inner">
                                                {{ substr($coin['symbol'], 0, 1) }}
                                            </div>
                                            <div class="min-w-0">
                                                <div class="truncate text-sm font-bold text-white">{{ $coin['name'] }}</div>
                                                <div class="text-xs font-medium text-slate-500">{{ $coin['symbol'] }}</div>
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <div class="text-sm font-bold text-white">{{ $coin['score'] }}</div>
                                            <div class="text-xs font-semibold {{ str_starts_with($coin['change'], '+') ? 'text-emerald-400' : 'text-rose-400' }}">{{ $coin['change'] }}</div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-5 flex items-center justify-between rounded-2xl border border-cyan-300/10 bg-cyan-300/[0.04] px-4 py-3">
                                <div class="flex items-center gap-2 text-xs text-slate-400">
                                    <span class="h-2 w-2 rounded-full bg-cyan-300"></span>
                                    Skor dihitung dari data terukur
                                </div>
                                <span class="text-xs font-semibold text-cyan-300">Lihat analisis</span>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -left-3 top-16 hidden rounded-2xl border border-white/10 bg-slate-900/90 p-3 shadow-xl backdrop-blur sm:block animate-float-delayed">
                        <div class="flex items-center gap-2">
                            <div class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-400/10 text-emerald-300">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M4 14 8 10l3 3 5-7" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-500">Momentum 30D</div>
                                <div class="text-xs font-bold text-emerald-300">+8.42%</div>
                            </div>
                        </div>
                    </div>

                    <div class="absolute -right-4 bottom-20 hidden rounded-2xl border border-white/10 bg-slate-900/90 p-3 shadow-xl backdrop-blur sm:block animate-float-slow">
                        <div class="flex items-center gap-2">
                            <div class="grid h-8 w-8 place-items-center rounded-xl bg-violet-400/10 text-violet-300">
                                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                    <path d="M10 2.5 16 5v4.5c0 3.8-2.6 6.5-6 8-3.4-1.5-6-4.2-6-8V5l6-2.5Z" stroke="currentColor" stroke-width="1.5"/>
                                    <path d="m7.5 10 1.6 1.6 3.5-3.6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-slate-500">Data Quality</div>
                                <div class="text-xs font-bold text-violet-300">Lengkap</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="border-y border-white/5 bg-white/[0.02] py-5" aria-label="Teknologi dan kapabilitas">
            <div class="mx-auto flex max-w-7xl flex-wrap items-center justify-center gap-x-10 gap-y-4 px-4 text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 sm:px-6 lg:px-8">
                <span>Laravel</span>
                <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                <span>Tailwind CSS</span>
                <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                <span>CoinGecko API</span>
                <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                <span>Metode SAW</span>
                <span class="h-1 w-1 rounded-full bg-slate-700"></span>
                <span>Explainable Ranking</span>
            </div>
        </section>

        <section id="fitur" class="scroll-mt-24 px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
            <div class="mx-auto max-w-7xl">
                <div class="mx-auto max-w-3xl text-center reveal-on-scroll">
                    <div class="text-sm font-bold uppercase tracking-[0.22em] text-cyan-300">Fitur utama</div>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Semua yang dibutuhkan untuk keputusan yang lebih terstruktur
                    </h2>
                    <p class="mt-5 text-base leading-8 text-slate-400 sm:text-lg">
                        Tidak hanya menampilkan harga. Sistem menyatukan ranking, analisis kontribusi, watchlist, dan perbandingan dalam satu alur yang mudah dipahami.
                    </p>
                </div>

                <div class="mt-14 grid gap-5 md:grid-cols-2 lg:grid-cols-3">
                    @php
                        $features = [
                            [
                                'title' => 'Ranking Global',
                                'description' => 'Admin mengelola ranking global berisi cryptocurrency terpilih berdasarkan data pasar terbaru.',
                                'icon' => 'ranking',
                                'accent' => 'cyan',
                            ],
                            [
                                'title' => 'Ranking Pribadi',
                                'description' => 'User membentuk alternatif sendiri dan memperoleh ranking sesuai coin yang ingin dianalisis.',
                                'icon' => 'user',
                                'accent' => 'blue',
                            ],
                            [
                                'title' => 'Analisis Kontribusi',
                                'description' => 'Nilai asli, normalisasi, bobot, dan kontribusi setiap kriteria ditampilkan secara transparan.',
                                'icon' => 'chart',
                                'accent' => 'violet',
                            ],
                            [
                                'title' => 'Watchlist Independen',
                                'description' => 'Pantau coin pilihan tanpa mencampurkannya dengan daftar alternatif dalam Ranking Saya.',
                                'icon' => 'eye',
                                'accent' => 'emerald',
                            ],
                            [
                                'title' => 'Perbandingan Coin',
                                'description' => 'Bandingkan performa beberapa coin menggunakan indeks yang setara dan lebih mudah dibaca.',
                                'icon' => 'compare',
                                'accent' => 'amber',
                            ],
                            [
                                'title' => 'Data Berkualitas',
                                'description' => 'Data tidak lengkap tidak diperlakukan sebagai nol, sehingga hasil ranking tetap lebih dapat dipercaya.',
                                'icon' => 'shield',
                                'accent' => 'rose',
                            ],
                        ];
                    @endphp

                    @foreach ($features as $index => $feature)
                        <article class="feature-card reveal-on-scroll" style="--reveal-delay: {{ ($index % 3) * 90 }}ms">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl border border-cyan-300/10 bg-gradient-to-br from-cyan-300/10 to-blue-500/10 text-cyan-300">
                                @switch($feature['icon'])
                                    @case('ranking')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 20V10M12 20V4M17 20v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/><path d="M4 20h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        @break
                                    @case('user')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="8" r="3.5" stroke="currentColor" stroke-width="1.8"/><path d="M5.5 20c.5-4 2.7-6 6.5-6s6 2 6.5 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"/></svg>
                                        @break
                                    @case('chart')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m4 16 5-5 4 3 7-8" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/><path d="M16 6h4v4" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @break
                                    @case('eye')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M2.8 12s3.2-6 9.2-6 9.2 6 9.2 6-3.2 6-9.2 6-9.2-6-9.2-6Z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="2.5" stroke="currentColor" stroke-width="1.8"/></svg>
                                        @break
                                    @case('compare')
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M7 7h13m0 0-3-3m3 3-3 3M17 17H4m0 0 3 3m-3-3 3-3" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                        @break
                                    @default
                                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3 20 6v6c0 5-3.4 8.3-8 10-4.6-1.7-8-5-8-10V6l8-3Z" stroke="currentColor" stroke-width="1.8"/><path d="m8.5 12 2.2 2.2 4.8-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                @endswitch
                            </div>
                            <h3 class="mt-5 text-lg font-bold text-white">{{ $feature['title'] }}</h3>
                            <p class="mt-3 text-sm leading-7 text-slate-400">{{ $feature['description'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="metode" class="scroll-mt-24 border-y border-white/5 bg-white/[0.018] px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
            <div class="mx-auto grid max-w-7xl items-center gap-14 lg:grid-cols-2">
                <div class="reveal-on-scroll">
                    <div class="text-sm font-bold uppercase tracking-[0.22em] text-cyan-300">Bagaimana sistem bekerja</div>
                    <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                        Dari data pasar menjadi ranking yang dapat dijelaskan
                    </h2>
                    <p class="mt-5 text-base leading-8 text-slate-400 sm:text-lg">
                        SAW mengubah setiap nilai kriteria ke skala yang sebanding, mengalikannya dengan bobot, lalu menjumlahkan kontribusi untuk memperoleh skor preferensi.
                    </p>

                    <div class="mt-9 space-y-4">
                        @php
                            $steps = [
                                ['number' => '01', 'title' => 'Ambil data pasar', 'text' => 'Harga, market cap, volume, momentum, dan histori harga diambil dari sumber data.'],
                                ['number' => '02', 'title' => 'Validasi dan normalisasi', 'text' => 'Data tidak lengkap dikeluarkan, kemudian setiap nilai dinormalisasi berdasarkan tipe benefit atau cost.'],
                                ['number' => '03', 'title' => 'Hitung skor SAW', 'text' => 'Nilai normalisasi dikalikan bobot kriteria dan dijumlahkan menjadi skor akhir.'],
                                ['number' => '04', 'title' => 'Tampilkan ranking', 'text' => 'Coin diurutkan berdasarkan skor dan kontribusi setiap kriteria dapat ditinjau.'],
                            ];
                        @endphp

                        @foreach ($steps as $step)
                            <div class="group flex gap-4 rounded-2xl border border-white/5 bg-white/[0.025] p-4 transition hover:border-cyan-300/10 hover:bg-white/[0.04]">
                                <div class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-cyan-300/10 text-xs font-extrabold text-cyan-300">{{ $step['number'] }}</div>
                                <div>
                                    <h3 class="font-bold text-white">{{ $step['title'] }}</h3>
                                    <p class="mt-1 text-sm leading-6 text-slate-400">{{ $step['text'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="reveal-on-scroll reveal-delay-2">
                    <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-slate-900/70 p-5 shadow-2xl shadow-black/30 backdrop-blur sm:p-7">
                        <div aria-hidden="true" class="absolute -right-20 -top-20 h-56 w-56 rounded-full bg-cyan-500/10 blur-3xl"></div>
                        <div class="relative">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-[0.18em] text-slate-500">Contoh kalkulasi</div>
                                    <div class="mt-1 text-xl font-bold text-white">Kontribusi Skor BTC</div>
                                </div>
                                <div class="rounded-xl bg-cyan-300/10 px-3 py-2 text-xs font-bold text-cyan-300">Score 0.9124</div>
                            </div>

                            <div class="mt-8 space-y-5">
                                @php
                                    $bars = [
                                        ['name' => 'Market Cap', 'weight' => '25%', 'width' => 96, 'value' => '0.240'],
                                        ['name' => 'Volume 24 Jam', 'weight' => '20%', 'width' => 86, 'value' => '0.172'],
                                        ['name' => 'Perubahan 24 Jam', 'weight' => '5%', 'width' => 62, 'value' => '0.031'],
                                        ['name' => 'Perubahan 7 Hari', 'weight' => '10%', 'width' => 74, 'value' => '0.074'],
                                        ['name' => 'Perubahan 30 Hari', 'weight' => '15%', 'width' => 81, 'value' => '0.122'],
                                        ['name' => 'Volatilitas 30 Hari', 'weight' => '25%', 'width' => 91, 'value' => '0.228'],
                                    ];
                                @endphp

                                @foreach ($bars as $bar)
                                    <div>
                                        <div class="mb-2 flex items-center justify-between gap-3 text-xs">
                                            <div class="font-semibold text-slate-300">{{ $bar['name'] }} <span class="font-normal text-slate-600">{{ $bar['weight'] }}</span></div>
                                            <div class="font-bold text-white">{{ $bar['value'] }}</div>
                                        </div>
                                        <div class="h-2 overflow-hidden rounded-full bg-white/5">
                                            <div class="h-full rounded-full bg-gradient-to-r from-cyan-400 to-blue-500 shadow-[0_0_18px_rgba(34,211,238,.25)]" style="width: {{ $bar['width'] }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mt-8 rounded-2xl border border-emerald-400/10 bg-emerald-400/[0.04] p-4">
                                <div class="flex gap-3">
                                    <div class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-emerald-400/10 text-emerald-300">
                                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m5.5 10 3 3 6-6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                    </div>
                                    <p class="text-sm leading-6 text-slate-300">
                                        BTC memperoleh kontribusi kuat dari market cap, volume, dan volatilitas yang lebih stabil dibandingkan alternatif lain.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="kriteria" class="scroll-mt-24 px-4 py-24 sm:px-6 lg:px-8 lg:py-32">
            <div class="mx-auto max-w-7xl">
                <div class="grid items-end gap-8 lg:grid-cols-[1fr_auto]">
                    <div class="max-w-3xl reveal-on-scroll">
                        <div class="text-sm font-bold uppercase tracking-[0.22em] text-cyan-300">Kriteria dan bobot</div>
                        <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl lg:text-5xl">
                            Seimbang antara ukuran pasar, momentum, dan risiko
                        </h2>
                        <p class="mt-5 text-base leading-8 text-slate-400 sm:text-lg">
                            Total bobot selalu 100%. Kriteria benefit memberi nilai lebih tinggi untuk performa yang lebih baik, sedangkan volatilitas diperlakukan sebagai cost.
                        </p>
                    </div>
                    <div class="reveal-on-scroll rounded-2xl border border-cyan-300/15 bg-cyan-300/[0.05] px-5 py-4 text-center">
                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-500">Total bobot</div>
                        <div class="mt-1 text-3xl font-extrabold text-cyan-300">100%</div>
                    </div>
                </div>

                <div class="mt-12 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @php
                        $criteria = [
                            ['name' => 'Market Cap', 'weight' => 25, 'type' => 'Benefit', 'description' => 'Mengukur skala dan kematangan pasar aset.'],
                            ['name' => 'Volume 24 Jam', 'weight' => 20, 'type' => 'Benefit', 'description' => 'Menggambarkan aktivitas dan likuiditas perdagangan.'],
                            ['name' => 'Perubahan 24 Jam', 'weight' => 5, 'type' => 'Benefit', 'description' => 'Menangkap momentum harga jangka sangat pendek.'],
                            ['name' => 'Perubahan 7 Hari', 'weight' => 10, 'type' => 'Benefit', 'description' => 'Mengukur momentum mingguan yang lebih stabil.'],
                            ['name' => 'Perubahan 30 Hari', 'weight' => 15, 'type' => 'Benefit', 'description' => 'Menilai tren performa dalam periode bulanan.'],
                            ['name' => 'Volatilitas 30 Hari', 'weight' => 25, 'type' => 'Cost', 'description' => 'Mengukur risiko berdasarkan penyebaran return harian.'],
                        ];
                    @endphp

                    @foreach ($criteria as $index => $criterion)
                        <article class="criterion-card reveal-on-scroll" style="--reveal-delay: {{ ($index % 3) * 80 }}ms">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="text-xs font-bold uppercase tracking-[0.16em] {{ $criterion['type'] === 'Benefit' ? 'text-emerald-300' : 'text-rose-300' }}">{{ $criterion['type'] }}</div>
                                    <h3 class="mt-2 text-lg font-bold text-white">{{ $criterion['name'] }}</h3>
                                </div>
                                <div class="text-2xl font-extrabold text-white">{{ $criterion['weight'] }}<span class="text-sm text-cyan-300">%</span></div>
                            </div>
                            <p class="mt-3 min-h-[3.25rem] text-sm leading-6 text-slate-400">{{ $criterion['description'] }}</p>
                            <div class="mt-5 h-2 overflow-hidden rounded-full bg-white/5">
                                <div class="h-full rounded-full bg-gradient-to-r {{ $criterion['type'] === 'Benefit' ? 'from-emerald-400 to-cyan-400' : 'from-rose-400 to-violet-400' }}" style="width: {{ $criterion['weight'] * 4 }}%"></div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="tentang" class="scroll-mt-24 px-4 pb-24 sm:px-6 lg:px-8 lg:pb-32">
            <div class="mx-auto max-w-7xl">
                <div class="relative overflow-hidden rounded-[2rem] border border-white/10 bg-gradient-to-br from-cyan-400/10 via-blue-500/10 to-violet-500/10 px-6 py-12 shadow-2xl shadow-black/30 sm:px-10 lg:px-14 lg:py-16 reveal-on-scroll">
                    <div aria-hidden="true" class="absolute inset-0 crypto-grid opacity-25"></div>
                    <div aria-hidden="true" class="absolute -right-24 -top-24 h-72 w-72 rounded-full bg-cyan-400/15 blur-3xl"></div>
                    <div class="relative grid items-center gap-8 lg:grid-cols-[1fr_auto]">
                        <div class="max-w-3xl">
                            <div class="text-sm font-bold uppercase tracking-[0.22em] text-cyan-300">Mulai analisis</div>
                            <h2 class="mt-4 text-3xl font-extrabold tracking-tight text-white sm:text-4xl">
                                Bangun ranking cryptocurrency Anda sendiri
                            </h2>
                            <p class="mt-4 text-base leading-8 text-slate-300">
                                Daftar, pilih coin yang ingin dibandingkan, perbarui data pasar, dan lihat bagaimana setiap kriteria membentuk skor akhir.
                            </p>
                            <p class="mt-3 text-xs leading-6 text-slate-500">
                                Sistem ini merupakan alat pendukung keputusan dan bukan nasihat keuangan atau jaminan keuntungan.
                            </p>
                        </div>
                        <div class="flex flex-col gap-3 sm:flex-row lg:flex-col">
                            @auth
                                <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-7 py-4 text-sm font-extrabold text-slate-950 transition hover:-translate-y-0.5 hover:bg-cyan-100">
                                    Masuk ke Dashboard
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                            @else
                                <a href="{{ route('register') }}" class="inline-flex items-center justify-center gap-2 rounded-2xl bg-white px-7 py-4 text-sm font-extrabold text-slate-950 transition hover:-translate-y-0.5 hover:bg-cyan-100">
                                    Daftar Gratis
                                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" aria-hidden="true"><path d="m7.5 5 5 5-5 5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </a>
                                <a href="{{ route('login') }}" class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/5 px-7 py-4 text-sm font-bold text-white transition hover:bg-white/10">Masuk</a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="relative z-10 border-t border-white/5 bg-slate-950/70 px-4 py-10 sm:px-6 lg:px-8">
        <div class="mx-auto flex max-w-7xl flex-col gap-6 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <span class="grid h-10 w-10 place-items-center rounded-2xl bg-gradient-to-br from-cyan-400 to-blue-600 text-white">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M5 16.5 9 12l3 3 7-8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/><path d="M14 7h5v5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </span>
                <div>
                    <div class="text-sm font-extrabold text-white">CryptoRank SAW</div>
                    <div class="text-xs text-slate-500">Sistem Pendukung Keputusan Cryptocurrency</div>
                </div>
            </div>
            <div class="text-xs leading-6 text-slate-500 sm:text-right">
                <div>&copy; {{ date('Y') }} CryptoRank SAW. Dibangun untuk kebutuhan akademik.</div>
                <div>Data pasar digunakan sebagai bahan analisis, bukan rekomendasi transaksi.</div>
            </div>
        </div>
    </footer>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll('.reveal-on-scroll');

        if (!('IntersectionObserver' in window) || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            elements.forEach((element) => element.classList.add('is-visible'));
            return;
        }

        const observer = new IntersectionObserver((entries, currentObserver) => {
            entries.forEach((entry) => {
                if (!entry.isIntersecting) return;

                entry.target.classList.add('is-visible');
                currentObserver.unobserve(entry.target);
            });
        }, {
            threshold: 0.12,
            rootMargin: '0px 0px -60px 0px',
        });

        elements.forEach((element) => observer.observe(element));
    });
</script>
@endpush
