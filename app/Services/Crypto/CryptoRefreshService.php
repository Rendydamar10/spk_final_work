<?php

namespace App\Services\Crypto;

use App\Models\ApiLog;
use App\Models\CryptoCoin;
use App\Models\RankingSet;
use App\Models\User;
use App\Models\Watchlist;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CryptoRefreshService
{
    private array $stablecoinSymbols = [
        'usdt', 'usdc', 'dai', 'busd', 'tusd', 'usdp', 'usdd', 'frax',
        'lusd', 'gusd', 'susd', 'pyusd', 'fdusd', 'usde', 'usds', 'usd1',
    ];

    public function __construct(
        private readonly CoinGeckoService $coinGeckoService,
        private readonly SawService $sawService,
        private readonly VolatilityCalculator $volatilityCalculator,
        private readonly CoinLogoStorageService $coinLogoStorageService,
    ) {}

    public function refreshTopCoins(int $limit = 10, ?User $admin = null): Collection
    {
        if ($admin && $admin->role !== 'admin') {
            throw new AuthorizationException('Hanya admin yang dapat melakukan refresh ranking global.');
        }

        try {
            $markets = collect($this->coinGeckoService->markets(1, 100))
                ->reject(fn (array $item) => $this->isStablecoin($item))
                ->take($limit)
                ->values();

            $preparedMarkets = $this->attachVolatility30d($markets);

            return DB::transaction(function () use ($preparedMarkets, $limit, $admin) {
                $globalRankingSet = RankingSet::globalSet($admin?->id);
                $coins = $preparedMarkets->map(fn (array $item) => $this->saveCoin($item));

                $globalRankingSet->coins()->sync($coins->pluck('id')->all());
                $ranked = $this->sawService->calculateForRankingSet($globalRankingSet);

                ApiLog::create([
                    'provider' => 'coingecko',
                    'action' => 'refresh_global_ranking',
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Berhasil refresh '.$coins->count().' coin; '.$ranked->count().' coin memiliki data lengkap untuk SAW.',
                    'meta' => [
                        'limit' => $limit,
                        'ranking_set_id' => $globalRankingSet->id,
                        'ranked_count' => $ranked->count(),
                    ],
                ]);

                return $coins;
            });
        } catch (Throwable $e) {
            $this->logFailure('refresh_global_ranking', $e);
            throw $e;
        }
    }

    public function refreshUserRanking(User $user): Collection
    {
        $rankingSet = RankingSet::userSet($user->id);
        $coinIds = $rankingSet->coins()->pluck('coingecko_id')->all();

        if (empty($coinIds)) {
            return $this->sawService->calculateForRankingSet($rankingSet);
        }

        try {
            $markets = collect();

            foreach (array_chunk($coinIds, 100) as $chunk) {
                $markets = $markets->concat($this->coinGeckoService->marketByIds($chunk));
            }

            $preparedMarkets = $this->attachVolatility30d($markets->values());

            return DB::transaction(function () use ($preparedMarkets, $rankingSet, $user) {
                $preparedMarkets->each(fn (array $item) => $this->saveCoin($item));
                $ranked = $this->sawService->calculateForRankingSet($rankingSet);

                ApiLog::create([
                    'provider' => 'coingecko',
                    'action' => 'refresh_user_ranking',
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Data ranking pribadi berhasil diperbarui.',
                    'meta' => [
                        'user_id' => $user->id,
                        'ranking_set_id' => $rankingSet->id,
                        'market_count' => $preparedMarkets->count(),
                        'ranked_count' => $ranked->count(),
                    ],
                ]);

                return $ranked;
            });
        } catch (Throwable $e) {
            $this->logFailure('refresh_user_ranking', $e, ['user_id' => $user->id]);
            throw $e;
        }
    }

    public function addCoinToUserRanking(string $coinGeckoId, User $user): ?CryptoCoin
    {
        try {
            $items = collect($this->coinGeckoService->marketByIds([$coinGeckoId]));

            if ($items->isEmpty()) {
                return null;
            }

            $item = $this->attachVolatility30d($items)->first();

            return DB::transaction(function () use ($item, $user, $coinGeckoId) {
                $coin = $this->saveCoin($item);
                $userRankingSet = RankingSet::userSet($user->id);

                $userRankingSet->coins()->syncWithoutDetaching([$coin->id]);
                $this->sawService->calculateForRankingSet($userRankingSet);

                ApiLog::create([
                    'provider' => 'coingecko',
                    'action' => 'add_user_ranking_coin',
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Berhasil menambahkan coin '.$coin->name.' ke ranking pribadi.',
                    'meta' => [
                        'coingecko_id' => $coinGeckoId,
                        'ranking_set_id' => $userRankingSet->id,
                        'user_id' => $user->id,
                    ],
                ]);

                return $coin;
            });
        } catch (Throwable $e) {
            $this->logFailure('add_user_ranking_coin', $e, [
                'coingecko_id' => $coinGeckoId,
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    public function addCoinToWatchlist(string $coinGeckoId, User $user): ?CryptoCoin
    {
        try {
            $items = collect($this->coinGeckoService->marketByIds([$coinGeckoId]));

            if ($items->isEmpty()) {
                return null;
            }

            $item = $this->attachVolatility30d($items)->first();

            return DB::transaction(function () use ($item, $user, $coinGeckoId) {
                $coin = $this->saveCoin($item);

                Watchlist::firstOrCreate([
                    'user_id' => $user->id,
                    'crypto_coin_id' => $coin->id,
                ]);

                ApiLog::create([
                    'provider' => 'coingecko',
                    'action' => 'add_watchlist_coin',
                    'status' => 'success',
                    'status_code' => 200,
                    'message' => 'Berhasil menambahkan coin '.$coin->name.' ke watchlist.',
                    'meta' => [
                        'coingecko_id' => $coinGeckoId,
                        'user_id' => $user->id,
                    ],
                ]);

                return $coin;
            });
        } catch (Throwable $e) {
            $this->logFailure('add_watchlist_coin', $e, [
                'coingecko_id' => $coinGeckoId,
                'user_id' => $user->id,
            ]);
            throw $e;
        }
    }

    public function addCoinByCoinGeckoId(string $coinGeckoId): ?CryptoCoin
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthorizationException('User harus login untuk menambahkan coin.');
        }

        return $this->addCoinToUserRanking($coinGeckoId, $user);
    }

    private function attachVolatility30d(Collection $items): Collection
    {
        return $items->map(function (array $item): array {
            $coinGeckoId = $item['id'] ?? null;

            if (!$coinGeckoId) {
                return $item;
            }
$existingCoin = CryptoCoin::query()
    ->where('coingecko_id', $coinGeckoId)
    ->first();

if (
    $existingCoin !== null &&
    $existingCoin->volatility !== null &&
    $existingCoin->last_synced_at !== null &&
    $existingCoin->last_synced_at->greaterThan(now()->subHours(6))
) {
    $item['_volatility_30d'] =
        (float) $existingCoin->volatility;

    return $item;
}
            try {
                $chart = $this->coinGeckoService->marketChart($coinGeckoId, 30);
                $volatility = $this->volatilityCalculator->calculateFromPriceSeries($chart['prices'] ?? []);

                if ($volatility !== null) {
                    $item['_volatility_30d'] = $volatility;
                }
            } catch (Throwable $e) {
                logger()->warning('Gagal mengambil volatilitas 30 hari.', [
                    'coingecko_id' => $coinGeckoId,
                    'error' => $e->getMessage(),
                ]);
            }

            return $item;
        });
    }

    private function saveCoin(array $item): CryptoCoin
    {
        $coinGeckoId = (string) $item['id'];
        $existing = CryptoCoin::where('coingecko_id', $coinGeckoId)->first();
        $volatility = array_key_exists('_volatility_30d', $item)
            ? $item['_volatility_30d']
            : $existing?->volatility;
        $logoSourceUrl = is_string($item['image'] ?? null) ? $item['image'] : null;
        $logoPath = $this->coinLogoStorageService->sync(
            $existing,
            $coinGeckoId,
            $logoSourceUrl
        );

        return CryptoCoin::updateOrCreate(
            ['coingecko_id' => $coinGeckoId],
            [
                'symbol' => strtolower($item['symbol'] ?? ''),
                'name' => $item['name'] ?? $coinGeckoId,
                'image' => $logoSourceUrl,
                'logo_path' => $logoPath,
                'logo_source_url' => $logoPath ? $logoSourceUrl : $existing?->logo_source_url,
                'current_price' => $this->nullableFloat($item, 'current_price'),
                'market_cap' => $this->nullableFloat($item, 'market_cap'),
                'market_cap_rank' => isset($item['market_cap_rank']) ? (int) $item['market_cap_rank'] : null,
                'total_volume' => $this->nullableFloat($item, 'total_volume'),
                'price_change_percentage_24h' => $this->nullableFloat($item, 'price_change_percentage_24h'),
                'price_change_percentage_7d_in_currency' => $this->nullableFloat($item, 'price_change_percentage_7d_in_currency'),
                'price_change_percentage_30d_in_currency' => $this->nullableFloat($item, 'price_change_percentage_30d_in_currency'),
                'volatility' => $volatility,
                'source_api' => 'coingecko',
                'is_stablecoin' => $this->isStablecoin($item),
                'is_active' => true,
                'last_synced_at' => now(),
                'raw_data' => Arr::except($item, ['_volatility_30d']),
            ]
        );
    }

    private function nullableFloat(array $item, string $key): ?float
    {
        return array_key_exists($key, $item) && is_numeric($item[$key])
            ? (float) $item[$key]
            : null;
    }

    private function isStablecoin(array $item): bool
    {
        $symbol = strtolower($item['symbol'] ?? '');
        $name = strtolower($item['name'] ?? '');

        if (in_array($symbol, $this->stablecoinSymbols, true)) {
            return true;
        }

        return str_contains($name, 'stablecoin');
    }

    private function logFailure(string $action, Throwable $e, array $meta = []): void
    {
        ApiLog::create([
            'provider' => 'coingecko',
            'action' => $action,
            'status' => 'failed',
            'status_code' => null,
            'message' => $e->getMessage(),
            'meta' => $meta,
        ]);
    }
}