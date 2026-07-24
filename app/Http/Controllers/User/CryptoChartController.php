<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\CryptoCoin;
use App\Services\Crypto\CoinGeckoChartService;
use Illuminate\Http\Request;

class CryptoChartController extends Controller
{
    public function show(CryptoCoin $cryptoCoin, CoinGeckoChartService $chartService)
    {
        abort_unless(
            auth()->user()
                ->watchlists()
                ->where('crypto_coin_id', $cryptoCoin->id)
                ->exists(),
            403
        );

        return response()->json([
            'coin' => [
                'id' => $cryptoCoin->id,
                'name' => $cryptoCoin->name,
                'symbol' => strtoupper($cryptoCoin->symbol),
            ],
            'chart' => $chartService->getMarketChart(
                coinId: $cryptoCoin->coingecko_id,
                currency: config('crypto.currency', 'usd'),
                days: 7
            ),
        ]);
    }

    public function compare(Request $request, CoinGeckoChartService $chartService)
    {
        $validated = $request->validate([
            'coin_ids' => ['required', 'array', 'min:2', 'max:5'],
            'coin_ids.*' => ['integer', 'exists:crypto_coins,id'],
        ]);

        $watchlistCoinIds = auth()->user()
            ->watchlists()
            ->whereIn('crypto_coin_id', $validated['coin_ids'])
            ->pluck('crypto_coin_id')
            ->toArray();

        $coins = CryptoCoin::whereIn('id', $watchlistCoinIds)->get();

        $datasets = $coins->map(function ($coin) use ($chartService) {
            $chart = $chartService->getMarketChart(
                coinId: $coin->coingecko_id,
                currency: config('crypto.currency', 'usd'),
                days: 7
            );

            return [
                'label' => strtoupper($coin->symbol),
                'name' => $coin->name,
                'prices' => $chart['prices'],
                'indexed_prices' => $chart['indexed_prices'],
                'labels' => $chart['labels'],
            ];
        })->values();

        return response()->json([
            'labels' => $datasets->first()['labels'] ?? [],
            'datasets' => $datasets,
        ]);
    }
}
