@extends('layouts.app')

@section('title', 'Ranking Cryptocurrency Saya')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 class="font-bold text-lg">Cari Cryptocurrency</h2>
        <p class="text-sm text-slate-500 mt-1">Tambahkan coin ke ranking pribadi Anda.</p>

        <form method="GET" action="{{ route('user.ranking.index') }}" class="mt-4 space-y-3">
            <input
                name="q"
                value="{{ request('q') }}"
                class="w-full px-4 py-3 rounded-xl border border-slate-300"
                placeholder="Contoh: bitcoin, chainlink, litecoin"
            >

            <button class="w-full px-4 py-3 rounded-xl bg-slate-900 text-white">
                Cari
            </button>
        </form>

        @if(!empty($searchResults))
            <div class="mt-5 space-y-2">
                @foreach($searchResults as $coin)
                    <form
                        method="POST"
                        action="{{ route('user.ranking.coins.store') }}"
                        class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3"
                        onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Menambah...';"
                    >
                        @csrf
                        <input type="hidden" name="coingecko_id" value="{{ $coin['id'] }}">

                        <div class="flex items-center gap-3">
                            <div>
                            <div class="font-semibold">{{ $coin['name'] ?? $coin['id'] }}</div>
                            <div class="text-xs text-slate-500">{{ strtoupper($coin['symbol'] ?? '-') }} / {{ $coin['id'] }}</div>
                            </div>
                        </div>

                        <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs disabled:opacity-60">
                            Tambah
                        </button>
                    </form>
                @endforeach
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-lg">Ranking Cryptocurrency Saya</h2>
                <p class="text-sm text-slate-500 mt-1">
                    Ranking ini hanya milik akun Anda dan tidak masuk ke Dashboard global.
                </p>
            </div>

            <form method="POST" action="{{ route('user.ranking.recalculate') }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Menghitung...';">
                @csrf
                <button class="px-4 py-3 rounded-xl bg-slate-900 text-white text-sm disabled:opacity-60">
                    Perbarui Data & Hitung SAW
                </button>
            </form>
        </div>

        <div class="mt-5 grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-sm text-slate-500">Total Coin</div>
                <div class="text-2xl font-bold mt-1">{{ $rankingCoinCount }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-sm text-slate-500">Hasil SAW</div>
                <div class="text-2xl font-bold mt-1">{{ $results->total() }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-sm text-slate-500">Data Tidak Lengkap</div>
                <div class="text-2xl font-bold mt-1">{{ $excludedCount }}</div>
            </div>
            <div class="rounded-xl border border-slate-200 p-4">
                <div class="text-sm text-slate-500">Terakhir Hitung</div>
                <div class="text-sm font-semibold mt-2">
                    {{ $rankingSet->results()->latest('calculated_at')->first()?->calculated_at?->format('d M Y H:i') ?? '-' }}
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200">
        <h2 class="font-bold text-lg">Hasil Ranking Pribadi</h2>
        <p class="text-sm text-slate-500">Normalisasi SAW hanya memakai coin yang ada pada Ranking Saya.</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="p-4 text-left">Rank</th>
                <th class="p-4 text-left">Coin</th>
                <th class="p-4 text-right">Harga</th>
                <th class="p-4 text-right">Market Cap</th>
                <th class="p-4 text-right">Volume</th>
                <th class="p-4 text-right">24h</th>
                <th class="p-4 text-right">7d</th>
                <th class="p-4 text-right">30d</th>
                <th class="p-4 text-right">Volatilitas 30d</th>
                <th class="p-4 text-right">Skor SAW</th>
                <th class="p-4 text-right">Aksi</th>
            </tr>
            </thead>
            <tbody>
            @forelse($results as $result)
                <tr class="border-t border-slate-100">
                    <td class="p-4 font-bold">#{{ $result->rank }}</td>
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <x-coin-logo :coin="$result->coin" />
                            <div>
                                <div class="font-semibold">{{ $result->coin->name }}</div>
                                <div class="text-xs text-slate-500">{{ strtoupper($result->coin->symbol) }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-right">{{ \App\Support\CryptoPriceFormatter::format($result->coin->current_price) }}</td>
                    <td class="p-4 text-right">${{ number_format((float) $result->coin->market_cap, 0) }}</td>
                    <td class="p-4 text-right">${{ number_format((float) $result->coin->total_volume, 0) }}</td>
                    <td class="p-4 text-right">{{ number_format((float) $result->coin->price_change_percentage_24h, 2) }}%</td>
                    <td class="p-4 text-right">{{ number_format((float) $result->coin->price_change_percentage_7d_in_currency, 2) }}%</td>
                    <td class="p-4 text-right">{{ number_format((float) $result->coin->price_change_percentage_30d_in_currency, 2) }}%</td>
                    <td class="p-4 text-right">{{ number_format((float) $result->coin->volatility, 4) }}%</td>
                    <td class="p-4 text-right font-bold">{{ number_format((float) $result->score, 6) }}</td>
                    <td class="p-4 text-right">
                        <form method="POST" action="{{ route('user.ranking.coins.destroy', $result->coin) }}">
                            @csrf
                            @method('DELETE')
                            <button onclick="return confirm('Hapus coin dari Ranking Saya?')" class="text-red-600">
                                Hapus
                            </button>
                        </form>
                        <details class="mt-2 text-left">
                            <summary class="cursor-pointer text-xs text-indigo-600">Lihat kontribusi</summary>
                            <div class="mt-2 min-w-64 rounded-lg border border-slate-200 bg-slate-50 p-3 text-xs">
                                @foreach($criteria as $criterion)
                                    <div class="flex justify-between gap-4 py-1">
                                        <span>{{ $criterion->name }}</span>
                                        <span class="font-semibold">{{ number_format((float) data_get($result->weighted_values, $criterion->code, 0), 6) }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="p-6 text-center text-slate-500">
                        Ranking pribadi belum memiliki coin. Cari dan tambahkan cryptocurrency terlebih dahulu.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">{{ $results->links() }}</div>
</div>
@endsection
