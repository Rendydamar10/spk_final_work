@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-sm text-slate-500">Coin Ranking Global</div>
        <div class="text-3xl font-bold mt-2">{{ $totalCoins }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-sm text-slate-500">Coin Terbaik</div>
        <div class="text-3xl font-bold mt-2">{{ $bestCoin?->coin?->symbol ? strtoupper($bestCoin->coin->symbol) : '-' }}</div>
        <div class="text-sm text-slate-500">Skor: {{ $bestCoin?->score ?? '-' }}</div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div class="text-sm text-slate-500">Refresh Terakhir</div>
        <div class="text-lg font-bold mt-2">{{ $lastLog?->created_at?->format('d M Y H:i') ?? '-' }}</div>
        <div class="text-sm text-slate-500">{{ $lastLog?->status ?? 'Belum ada log' }}</div>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-lg">Ranking Global Cryptocurrency</h2>
            <p class="text-sm text-slate-500">Hasil refresh admin, maksimal 10 coin, dihitung terpisah dari ranking pribadi user.</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <form method="POST" action="{{ route('admin.refresh.run') }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Memproses...';">
                @csrf
                <button class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm disabled:opacity-60">Refresh 10 Coin</button>
            </form>
        @endif
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="text-left p-4">Rank</th>
                <th class="text-left p-4">Coin</th>
                <th class="text-right p-4">Harga</th>
                <th class="text-right p-4">Market Cap</th>
                <th class="text-right p-4">Volume</th>
                <th class="text-right p-4">Skor</th>
                <th class="text-left p-4">Diperbarui</th>
            </tr>
            </thead>
            <tbody>
            @forelse($topResults as $result)
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
                    <td class="p-4 text-right font-bold">{{ number_format((float) $result->score, 6) }}</td>
                    <td class="p-4">{{ $result->calculated_at?->format('d M Y H:i') ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="p-6 text-center text-slate-500">Ranking global belum tersedia. Admin perlu melakukan refresh data cryptocurrency.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
