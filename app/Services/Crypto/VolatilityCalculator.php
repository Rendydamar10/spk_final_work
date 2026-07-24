<?php

namespace App\Services\Crypto;

class VolatilityCalculator
{
    /**
     * Menghitung volatilitas historis 30 hari sebagai sample standard deviation
     * dari return harian dalam satuan persen.
     *
     * @param  array<int, mixed>  $priceSeries CoinGecko format: [[timestamp_ms, price], ...]
     */
    public function calculateFromPriceSeries(array $priceSeries): ?float
    {
        $dailyPrices = $this->dailyClosingPrices($priceSeries);

        if (count($dailyPrices) < 3) {
            return null;
        }

        $returns = [];

        for ($index = 1; $index < count($dailyPrices); $index++) {
            $previous = $dailyPrices[$index - 1];
            $current = $dailyPrices[$index];

            if ($previous <= 0 || $current <= 0) {
                continue;
            }

            $returns[] = (($current / $previous) - 1) * 100;
        }

        if (count($returns) < 2) {
            return null;
        }

        $mean = array_sum($returns) / count($returns);
        $sumSquaredDeviation = array_sum(array_map(
            fn (float $return): float => ($return - $mean) ** 2,
            $returns
        ));

        $sampleVariance = $sumSquaredDeviation / (count($returns) - 1);

        return round(sqrt($sampleVariance), 8);
    }

    /**
     * Mengambil harga penutupan terakhir setiap tanggal UTC.
     * Juga menerima array harga numerik sederhana untuk kebutuhan test.
     *
     * @param  array<int, mixed>  $priceSeries
     * @return array<int, float>
     */
    private function dailyClosingPrices(array $priceSeries): array
    {
        $daily = [];
        $plain = [];

        foreach ($priceSeries as $point) {
            if (is_array($point) && count($point) >= 2 && is_numeric($point[0]) && is_numeric($point[1])) {
                $timestampSeconds = (int) floor(((float) $point[0]) / 1000);
                $date = gmdate('Y-m-d', $timestampSeconds);
                $daily[$date] = (float) $point[1];
                continue;
            }

            if (is_numeric($point)) {
                $plain[] = (float) $point;
            }
        }

        if (!empty($daily)) {
            ksort($daily);

            return array_values($daily);
        }

        return $plain;
    }
}
