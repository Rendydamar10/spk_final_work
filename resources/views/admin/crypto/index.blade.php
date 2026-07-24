@extends('layouts.app')

@section('title', 'Data Crypto')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm">
    <div class="p-5 border-b border-slate-200 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h2 class="font-bold text-lg">Data Crypto</h2>
            <p class="text-sm text-slate-500">Data disimpan dari API CoinGecko ke database lokal.</p>
        </div>
        <form method="GET" class="flex gap-2">
            <input name="search" value="{{ request('search') }}" class="px-4 py-2 rounded-xl border border-slate-300" placeholder="Cari coin...">
            <button class="px-4 py-2 rounded-xl bg-slate-900 text-white">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="p-4 text-left">Coin</th>
                <th class="p-4 text-right">Harga</th>
                <th class="p-4 text-right">Market Cap</th>
                <th class="p-4 text-right">Volume</th>
                <th class="p-4 text-right">24h</th>
                <th class="p-4 text-right">7d</th>
                <th class="p-4 text-right">Skor</th>
            </tr>
            </thead>
            <tbody>
            @forelse($coins as $coin)
                <tr class="border-t border-slate-100">
                    <td class="p-4">
                        <div class="flex items-center gap-3">
                            <x-coin-logo :coin="$coin" />
                            <div>
                                <div class="font-semibold">{{ $coin->name }}</div>
                                <div class="text-xs text-slate-500">{{ strtoupper($coin->symbol) }} / {{ $coin->coingecko_id }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="p-4 text-right">{{ \App\Support\CryptoPriceFormatter::format($coin->current_price) }}</td>
                    <td class="p-4 text-right">${{ number_format((float) $coin->market_cap, 0) }}</td>
                    <td class="p-4 text-right">${{ number_format((float) $coin->total_volume, 0) }}</td>
                    <td class="p-4 text-right">{{ number_format((float) $coin->price_change_percentage_24h, 2) }}%</td>
                    <td class="p-4 text-right">{{ number_format((float) $coin->price_change_percentage_7d_in_currency, 2) }}%</td>
                    <td class="p-4 text-right font-bold">{{ $coin->globalSawResult?->score ? number_format((float) $coin->globalSawResult->score, 6) : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="p-6 text-center text-slate-500">Belum ada data crypto.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">{{ $coins->links() }}</div>
</div>
@endsection
