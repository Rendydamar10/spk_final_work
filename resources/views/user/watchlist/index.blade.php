@extends('layouts.app')

@section('title', 'Tambah Coin / Watchlist')

@section('content')
@php
    $chartCoins = $watchlists->map(function ($item) {
        $coin = $item->coin;

        if (!$coin) {
            return null;
        }

        return [
            'id' => $coin->id,
            'name' => $coin->name,
            'symbol' => strtoupper($coin->symbol),
        ];
    })->filter()->values();
@endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1 bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
        <h2 class="font-bold text-lg">Cari Coin</h2>
        <p class="text-sm text-slate-500 mt-1">
            Cari coin dari CoinGecko, lalu tambahkan ke Watchlist.
        </p>

        <form method="GET" class="mt-4 space-y-3">
            <input 
                name="q" 
                value="{{ request('q') }}" 
                class="w-full px-4 py-3 rounded-xl border border-slate-300" 
                placeholder="Contoh: bitcoin, eth, solana"
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
                        action="{{ route('user.watchlist.store') }}" 
                        class="p-3 rounded-xl border border-slate-200 flex items-center justify-between gap-3"
                    >
                        @csrf

                        <input type="hidden" name="coingecko_id" value="{{ $coin['id'] }}">

                        <div class="flex items-center gap-3">
                            <div>
                                <div class="font-semibold">
                                    {{ $coin['name'] ?? $coin['id'] }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    {{ strtoupper($coin['symbol'] ?? '-') }} / {{ $coin['id'] }}
                                </div>
                            </div>
                        </div>

                        <button class="px-3 py-2 rounded-lg bg-indigo-600 text-white text-xs">
                            Tambah
                        </button>
                    </form>
                @endforeach
            </div>
        @endif
    </div>

    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="font-bold text-lg">Watchlist Saya</h2>
                    <p class="text-sm text-slate-500">
                        Watchlist dan Ranking Saya berdiri sendiri. Coin watchlist dapat dipakai di menu Bandingkan Coin.
                    </p>
                </div>

                <a 
                    href="{{ route('user.compare.index') }}" 
                    class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm"
                >
                    Bandingkan
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-500">
                        <tr>
                            <th class="p-4 text-left">Coin</th>
                            <th class="p-4 text-right">Rank</th>
                            <th class="p-4 text-right">Skor</th>
                            <th class="p-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($watchlists as $item)
                            @php($userResult = $userResultsByCoinId->get($item->crypto_coin_id))
                            <tr class="border-t border-slate-100">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <x-coin-logo :coin="$item->coin" />
                                        <div>
                                            <div class="font-semibold">{{ $item->coin->name ?? '-' }}</div>
                                            <div class="text-xs text-slate-500">
                                                {{ strtoupper($item->coin->symbol ?? '-') }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="p-4 text-right">
                                    {{ $userResult?->rank !== null ? '#'.$userResult->rank : '-' }}
                                </td>

                                <td class="p-4 text-right font-bold">
                                    {{ $userResult?->score !== null ? number_format((float) $userResult->score, 6) : '-' }}
                                </td>

                                <td class="p-4 text-right space-y-2">
                                    @if(!$userResult && $item->coin)
                                        <form method="POST" action="{{ route('user.ranking.coins.store') }}">
                                            @csrf
                                            <input type="hidden" name="coingecko_id" value="{{ $item->coin->coingecko_id }}">
                                            <button class="text-indigo-600">Tambah ke Ranking</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('user.watchlist.destroy', $item) }}">
                                        @csrf
                                        @method('DELETE')

                                        <button 
                                            onclick="return confirm('Hapus dari watchlist?')" 
                                            class="text-red-600"
                                        >
                                            Hapus
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="p-6 text-center text-slate-500">
                                    Watchlist masih kosong.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($watchlists->count() > 0)
            <div class="space-y-5">
                <h2 class="font-bold text-lg text-slate-900">
                    Chart Watchlist
                </h2>

                @foreach ($watchlists as $item)
                    @if($item->coin)
                        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-3">
                                    <x-coin-logo :coin="$item->coin" size="w-10 h-10" />
                                    <div>
                                    <h3 class="font-bold text-lg text-gray-900">
                                        {{ $item->coin->name }}
                                    </h3>
                                    <p class="text-sm text-gray-500">
                                        {{ strtoupper($item->coin->symbol) }}
                                    </p>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <p class="font-semibold text-gray-900">
                                        {{ \App\Support\CryptoPriceFormatter::format($item->coin->current_price) }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        Harga sekarang
                                    </p>
                                </div>
                            </div>

                            <div class="h-48">
                                <canvas id="chart-watchlist-{{ $item->coin->id }}"></canvas>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const coins = @json($chartCoins);

    coins.forEach(function (coin) {
        fetch(`/user/crypto/${coin.id}/chart`)
            .then(response => response.json())
            .then(data => {
                const canvas = document.getElementById(`chart-watchlist-${coin.id}`);

                if (!canvas || !data.chart || !data.chart.prices || data.chart.prices.length === 0) {
                    return;
                }

                new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: data.chart.labels,
                        datasets: [
                            {
                                label: coin.symbol + ' 7 Hari',
                                data: data.chart.prices,
                                tension: 0.35,
                                fill: false,
                                borderWidth: 2,
                                pointRadius: 0
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true
                            }
                        },
                        scales: {
                            x: {
                                ticks: {
                                    maxTicksLimit: 5
                                }
                            },
                            y: {
                                beginAtZero: false
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Gagal memuat chart:', error);
            });
    });
});
</script>
@endsection
