@extends('layouts.app')

@section('title', 'Bandingkan Coin')

@section('content')
@if($selected->count() < 2)
<div class="p-6 text-md mb-6 rounded-2xl bg-amber-50 border border-amber-200 text-amber-800">
    Pilih minimal 2 coin dari Watchlist untuk menghitung perbandingan SAW.
</div>
@endif
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 mb-6">
    <h2 class="font-bold text-lg">Pilih Coin dari Watchlist</h2>
    <p class="text-sm text-slate-500 mt-1">
        Pilih 2 sampai 5 coin untuk dibandingkan berdasarkan kriteria SAW.
    </p>


    <form method="GET" class="mt-4 flex flex-wrap gap-3 items-center">
        @forelse($watchlists as $item)
        @if($item->coin)
        <label class="px-4 py-3 rounded-xl border border-slate-300 flex items-center gap-2 cursor-pointer">
            <input type="checkbox" name="coins[]" value="{{ $item->crypto_coin_id }}"
                @checked(in_array($item->crypto_coin_id, $selectedIds, true))
            >

            <x-coin-logo :coin="$item->coin" size="w-7 h-7" />
            <span>
                {{ $item->coin->name }}
                <small class="text-slate-500">
                    {{ strtoupper($item->coin->symbol) }}
                </small>
            </span>
        </label>
        @endif
        @empty
        <div class="text-slate-500">
            Watchlist masih kosong. Tambahkan coin terlebih dahulu.
        </div>
        @endforelse

        @if($watchlists->isNotEmpty())
        <button class="px-5 py-3 rounded-xl bg-indigo-600 text-white font-semibold">
            Bandingkan
        </button>
        @endif
    </form>
</div>
@if($selected->count() >= 2)
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
    <div class="p-5 border-b border-slate-200">
        <h2 class="font-bold text-lg">Hasil Perbandingan SAW</h2>
        <p class="text-sm text-slate-500">
            Rank dan skor dihitung hanya dari coin yang dipilih di halaman ini, terpisah dari Ranking Saya.
        </p>
    </div>

    @if($excludedSelectedCount > 0)
        <div class="mx-5 mt-5 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
            {{ $excludedSelectedCount }} coin tidak dapat dihitung karena data kriterianya belum lengkap.
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-500">
                <tr>
                    <th class="p-4 text-left">Kriteria</th>
                    @foreach($selected as $item)
                    <th class="p-4 text-right">
                        <span class="inline-flex items-center justify-end gap-2">
                            <x-coin-logo :coin="$item->coin" size="w-6 h-6" />
                            {{ $item->coin->name ?? '-' }}
                        </span>
                    </th>
                    @endforeach
                </tr>
            </thead>

            <tbody>
                <tr class="border-t border-slate-100">
                    <td class="p-4 font-semibold">Harga</td>
                    @foreach($selected as $item)
                    <td class="p-4 text-right">
                        {{ \App\Support\CryptoPriceFormatter::format($item->coin->current_price) }}
                    </td>
                    @endforeach
                </tr>

                @foreach($criteria as $criterion)
                <tr class="border-t border-slate-100">
                    <td class="p-4 font-semibold">
                        {{ $criterion->name }}
                        <span class="text-xs text-slate-500">
                            ({{ $criterion->type }})
                        </span>
                    </td>

                    @foreach($selected as $item)
                    @php($criterionValue = $item->coin?->{$criterion->source_field})
                    <td class="p-4 text-right">
                        {{ $criterionValue === null ? '-' : number_format((float) $criterionValue, 6) }}
                    </td>
                    @endforeach
                </tr>
                @endforeach

                <tr class="border-t border-slate-100 bg-slate-50">
                    <td class="p-4 font-bold">Rank SAW</td>
                    @foreach($selected as $item)
                    @php($comparisonResult = $comparisonResultsByCoinId->get($item->crypto_coin_id))
                    <td class="p-4 text-right font-bold">
                        {{ $comparisonResult ? '#'.$comparisonResult['rank'] : '-' }}
                    </td>
                    @endforeach
                </tr>

                <tr class="border-t border-slate-100 bg-slate-50">
                    <td class="p-4 font-bold">Skor SAW</td>
                    @foreach($selected as $item)
                    @php($comparisonResult = $comparisonResultsByCoinId->get($item->crypto_coin_id))
                    <td class="p-4 text-right font-bold">
                        {{ $comparisonResult ? number_format((float) $comparisonResult['score'], 6) : '-' }}
                    </td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    </div>
</div>
@else

@endif
<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h2 class="text-xl font-bold text-gray-900 mb-4">
        Chart Perbandingan dari Watchlist
    </h2>

    <form id="comparisonForm" data-chart-url="{{ route('user.comparison.charts') }}">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-5">
            @foreach ($watchlists as $item)
            @if($item->coin)
            <label class="flex items-center gap-3 border rounded-xl p-4 cursor-pointer hover:bg-gray-50">
                <input type="checkbox" name="coin_ids[]" value="{{ $item->coin->id }}" class="rounded border-gray-300">

                <x-coin-logo :coin="$item->coin" />
                <div>
                    <p class="font-semibold text-gray-900">
                        {{ $item->coin->name }}
                    </p>
                    <p class="text-sm text-gray-500">
                        {{ strtoupper($item->coin->symbol) }}
                    </p>
                </div>
            </label>
            @endif
            @endforeach
        </div>

        @if($watchlists->isNotEmpty())
        <button type="submit" class="px-5 py-3 rounded-xl bg-slate-900 text-white font-semibold hover:bg-slate-800">
            Tampilkan Chart Perbandingan
        </button>
        @endif
    </form>
</div>

<div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
    <h3 class="text-lg font-bold text-gray-900 mb-4">
        Chart Performa Terindeks 7 Hari
    </h3>

    <div class="h-96">
        <canvas id="comparisonChart"></canvas>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let comparisonChart = null;

    document.addEventListener('DOMContentLoaded', function() {

        const comparisonForm = document.getElementById('comparisonForm');

        if (!comparisonForm) {
            return;
        }

        comparisonForm.addEventListener('submit', function(event) {

            event.preventDefault();

            const selectedCoins = Array.from(
                document.querySelectorAll('input[name="coin_ids[]"]:checked')
            ).map(input => input.value);

            if (selectedCoins.length < 2) {
                alert('Pilih minimal 2 coin untuk dibandingkan.');
                return;
            }

            if (selectedCoins.length > 5) {
                alert('Maksimal 5 coin untuk dibandingkan.');
                return;
            }

            const chartUrl = comparisonForm.dataset.chartUrl;

            fetch(chartUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({
                        coin_ids: selectedCoins
                    })
                })
                .then(response => response.json())
                .then(data => {

                    const canvas = document.getElementById('comparisonChart');

                    if (!canvas) {
                        return;
                    }

                    if (comparisonChart) {
                        comparisonChart.destroy();
                    }

                    comparisonChart = new Chart(canvas, {
                        type: 'line',
                        data: {
                            labels: data.labels || [],
                            datasets: (data.datasets || []).map(function(item) {
                                return {
                                    label: item.label,
                                    data: item.indexed_prices || item.prices,
                                    tension: 0.35,
                                    fill: false,
                                    borderWidth: 2,
                                    pointRadius: 0
                                };
                            })
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    display: true,
                                    position: 'top'
                                }
                            },
                            scales: {
                                x: {
                                    ticks: {
                                        maxTicksLimit: 7
                                    }
                                },
                                y: {
                                    beginAtZero: false,
                                    title: {
                                        display: true,
                                        text: 'Indeks performa (awal = 100)'
                                    }
                                }
                            }
                        }
                    });

                })
                .catch(error => {
                    console.error(error);
                    alert('Gagal memuat chart perbandingan.');
                });

        });

    });
</script>
@endsection