<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Criterion;
use App\Models\RankingSet;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index()
    {
        $results = RankingSet::globalSet()
            ->results()
            ->with('coin')
            ->orderBy('rank')
            ->get();

        return view('admin.reports.index', compact('results'));
    }

    public function exportCsv(): StreamedResponse
    {
        $filename = 'laporan-ranking-saw-'.now()->format('Ymd-His').'.csv';
        $criteria = Criterion::where('is_active', true)->ordered()->get();

        return response()->streamDownload(function () use ($criteria) {
            $handle = fopen('php://output', 'w');
            $headers = [
                'Rank',
                'Coin',
                'Symbol',
                'Harga',
                'Market Cap',
                'Volume 24 Jam',
                'Perubahan 24 Jam (%)',
                'Perubahan 7 Hari (%)',
                'Perubahan 30 Hari (%)',
                'Volatilitas 30 Hari (%)',
                'Skor SAW',
                'Tanggal Data',
                'Tanggal Hitung',
            ];

            foreach ($criteria as $criterion) {
                $headers[] = 'Nilai Asli - '.$criterion->name;
                $headers[] = 'Normalisasi - '.$criterion->name;
                $headers[] = 'Kontribusi - '.$criterion->name;
            }

            fputcsv($handle, $headers);

            RankingSet::globalSet()
                ->results()
                ->with('coin')
                ->orderBy('rank')
                ->chunk(100, function ($results) use ($handle, $criteria) {
                    foreach ($results as $result) {
                        $row = [
                            $result->rank,
                            $result->coin->name,
                            strtoupper($result->coin->symbol),
                            $result->coin->current_price,
                            $result->coin->market_cap,
                            $result->coin->total_volume,
                            $result->coin->price_change_percentage_24h,
                            $result->coin->price_change_percentage_7d_in_currency,
                            $result->coin->price_change_percentage_30d_in_currency,
                            $result->coin->volatility,
                            $result->score,
                            $result->coin->last_synced_at?->toDateTimeString(),
                            $result->calculated_at?->toDateTimeString(),
                        ];

                        foreach ($criteria as $criterion) {
                            $row[] = data_get($result->raw_values, $criterion->code);
                            $row[] = data_get($result->normalized_values, $criterion->code);
                            $row[] = data_get($result->weighted_values, $criterion->code);
                        }

                        fputcsv($handle, $row);
                    }
                });

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
