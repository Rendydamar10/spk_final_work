@extends('layouts.app')

@section('title', 'Ranking Global SAW')

@section('content')
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200 flex items-center justify-between">
        <div>
            <h2 class="font-bold text-lg">Ranking Global SAW</h2>
            <p class="text-sm text-slate-500">Nilai akhir global dihitung dari enam kriteria aktif. Setiap kolom menunjukkan kontribusi setelah normalisasi dan pembobotan.</p>
        </div>
        <form method="POST" action="{{ route('admin.ranking.recalculate') }}" onsubmit="this.querySelector('button').disabled = true; this.querySelector('button').innerText = 'Menghitung...';">
            @csrf
            <button class="px-4 py-2 rounded-xl bg-slate-900 text-white text-sm disabled:opacity-60">Hitung Ulang Global</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
            <tr>
                <th class="p-4 text-left">Rank</th>
                <th class="p-4 text-left">Coin</th>
                @foreach($criteria as $criterion)
                    <th class="p-4 text-right">Kontribusi {{ $criterion->name }}</th>
                @endforeach
                <th class="p-4 text-right">Skor</th>
            </tr>
            </thead>
            <tbody>
            @forelse($results as $result)
                <tr class="border-t border-slate-100">
                    <td class="p-4 font-bold">#{{ $result->rank }}</td>
                    <td class="p-4 font-semibold">{{ $result->coin->name }} <span class="text-xs text-slate-500">{{ strtoupper($result->coin->symbol) }}</span></td>
                    @foreach($criteria as $criterion)
                        <td class="p-4 text-right">{{ number_format((float) data_get($result->weighted_values, $criterion->code, 0), 6) }}</td>
                    @endforeach
                    <td class="p-4 text-right font-bold">{{ number_format((float) $result->score, 6) }}</td>
                </tr>
            @empty
                <tr><td colspan="{{ 3 + $criteria->count() }}" class="p-6 text-center text-slate-500">Belum ada ranking.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-4">{{ $results->links() }}</div>
</div>
@endsection
