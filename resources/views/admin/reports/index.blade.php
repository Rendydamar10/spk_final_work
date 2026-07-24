@extends('layouts.app')

@section('title', 'Laporan')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-lg">Laporan Ranking Global SAW</h2>
            <p class="text-sm text-slate-500">Export hasil ranking global untuk kebutuhan skripsi/laporan.</p>
        </div>
        <a href="{{ route('admin.reports.exportCsv') }}" class="px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm">Export CSV</a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="p-4 text-left">Rank</th>
                <th class="p-4 text-left">Coin</th>
                <th class="p-4 text-right">30 Hari</th>
                <th class="p-4 text-right">Volatilitas 30 Hari</th>
                <th class="p-4 text-right">Skor</th>
                <th class="p-4 text-left">Tanggal Hitung</th>
            </tr>
            </thead>
            <tbody>
            @forelse($results as $result)
                <tr class="border-t border-slate-100">
                    <td class="p-4 font-bold">#{{ $result->rank }}</td>
                    <td class="p-4">{{ $result->coin->name }} ({{ strtoupper($result->coin->symbol) }})</td>
                    <td class="p-4 text-right">{{ number_format((float) $result->coin->price_change_percentage_30d_in_currency, 2) }}%</td>
                    <td class="p-4 text-right">{{ number_format((float) $result->coin->volatility, 4) }}%</td>
                    <td class="p-4 text-right font-bold">{{ number_format((float) $result->score, 6) }}</td>
                    <td class="p-4">{{ $result->calculated_at?->format('d M Y H:i') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" class="p-6 text-center text-slate-500">Belum ada data laporan.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
