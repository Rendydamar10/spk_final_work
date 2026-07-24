<?php

namespace App\Services\Crypto;

class CoinGeckoChartService
{
    public function __construct(
        private readonly CoinGeckoService $coinGeckoService,
    ) {}

    public function getMarketChart(string $coinId, string $currency = 'usd', int $days = 7): array
    {
        $chart = $this->coinGeckoService->marketChart($coinId, $days, $currency);
        $points = collect($chart['prices'] ?? [])
            ->filter(fn ($item) => is_array($item) && count($item) >= 2 && is_numeric($item[0]) && is_numeric($item[1]))
            ->values();

        $prices = $points
            ->map(fn ($item) => round((float) $item[1], 8))
            ->all();

        $firstPrice = collect($prices)->first(fn ($price) => $price > 0);
        $indexedPrices = $firstPrice
            ? collect($prices)->map(fn ($price) => round(($price / $firstPrice) * 100, 6))->all()
            : [];

        return [
            'labels' => $points
                ->map(fn ($item) => date('d M H:i', ((int) $item[0]) / 1000))
                ->all(),
            'prices' => $prices,
            'indexed_prices' => $indexedPrices,
            'currency' => $currency,
        ];
    }
}
