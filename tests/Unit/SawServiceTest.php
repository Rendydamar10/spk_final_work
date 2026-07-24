<?php

namespace Tests\Unit;

use App\Models\Criterion;
use App\Models\CryptoCoin;
use App\Models\RankingSet;
use App\Models\User;
use App\Services\Crypto\SawService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SawServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_saw_calculation_uses_only_coins_in_requested_ranking_set(): void
    {
        $this->seedCriteria();

        $globalSet = RankingSet::globalSet();
        $user = User::factory()->create(['role' => 'user']);
        $userSet = RankingSet::userSet($user->id);

        $globalHigh = $this->coin('global-high', 1000, 1000, 5, 7, 10, 2);
        $globalLow = $this->coin('global-low', 100, 100, -2, 1, 2, 6);
        $userOnly = $this->coin('user-only', 5000, 3000, 8, 12, 20, 4);

        $globalSet->coins()->sync([$globalHigh->id, $globalLow->id]);
        $userSet->coins()->sync([$userOnly->id]);

        $service = app(SawService::class);
        $service->calculateForRankingSet($globalSet);
        $service->calculateForRankingSet($userSet);

        $this->assertSame(2, $globalSet->results()->count());
        $this->assertSame(1, $userSet->results()->count());
        $this->assertDatabaseMissing('saw_results', [
            'ranking_set_id' => $globalSet->id,
            'crypto_coin_id' => $userOnly->id,
        ]);
    }

    public function test_single_complete_coin_gets_score_one_and_raw_values_are_saved(): void
    {
        $this->seedCriteria();

        $user = User::factory()->create(['role' => 'user']);
        $rankingSet = RankingSet::userSet($user->id);
        $coin = $this->coin('solo', 1000, 900, 1, 2, 3, 2.5);
        $rankingSet->coins()->sync([$coin->id]);

        app(SawService::class)->calculateForRankingSet($rankingSet);
        $result = $rankingSet->results()->firstOrFail();

        $this->assertSame(1, (int) $result->rank);
        $this->assertSame(1.0, (float) $result->score);
        $this->assertSame(1000.0, (float) $result->raw_values['market_cap']);
        $this->assertArrayHasKey('price_change_percentage_30d_in_currency', $result->weighted_values);
    }

    public function test_coin_with_missing_active_criterion_is_excluded_instead_of_treated_as_zero(): void
    {
        $this->seedCriteria();

        $set = RankingSet::globalSet();
        $complete = $this->coin('complete', 1000, 900, 1, 2, 3, 2.5);
        $incomplete = $this->coin('incomplete', 2000, 1800, 2, 4, 6, null);
        $set->coins()->sync([$complete->id, $incomplete->id]);

        app(SawService::class)->calculateForRankingSet($set);

        $this->assertSame(1, $set->results()->count());
        $this->assertDatabaseMissing('saw_results', [
            'ranking_set_id' => $set->id,
            'crypto_coin_id' => $incomplete->id,
        ]);
    }


    public function test_invalid_total_weight_is_rejected(): void
    {
        Criterion::query()->update(['is_active' => false]);

        Criterion::updateOrCreate(
            ['code' => 'market_cap'],
            [
                'name' => 'Market Cap',
                'type' => 'benefit',
                'weight' => 0.75,
                'source_field' => 'market_cap',
                'is_active' => true,
            ]
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Total bobot kriteria aktif harus 1.0000');

        app(SawService::class)->calculateForRankingSet(RankingSet::globalSet());
    }

    public function test_invalid_criterion_type_is_rejected(): void
    {
        Criterion::query()->update(['is_active' => false]);

        Criterion::updateOrCreate(
            ['code' => 'market_cap'],
            [
                'name' => 'Market Cap',
                'type' => 'neutral',
                'weight' => 1,
                'source_field' => 'market_cap',
                'is_active' => true,
            ]
        );

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Tipe kriteria harus benefit atau cost');

        app(SawService::class)->calculateForRankingSet(RankingSet::globalSet());
    }

    private function seedCriteria(): void
    {
        $rows = [
            ['market_cap', 'Market Cap', 'benefit', 0.25, 'market_cap'],
            ['total_volume', 'Volume 24 Jam', 'benefit', 0.20, 'total_volume'],
            ['price_change_percentage_24h', 'Perubahan 24 Jam', 'benefit', 0.05, 'price_change_percentage_24h'],
            ['price_change_percentage_7d_in_currency', 'Perubahan 7 Hari', 'benefit', 0.10, 'price_change_percentage_7d_in_currency'],
            ['price_change_percentage_30d_in_currency', 'Perubahan 30 Hari', 'benefit', 0.15, 'price_change_percentage_30d_in_currency'],
            ['volatility', 'Volatilitas 30 Hari', 'cost', 0.25, 'volatility'],
        ];

        foreach ($rows as [$code, $name, $type, $weight, $field]) {
            Criterion::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'type' => $type,
                    'weight' => $weight,
                    'source_field' => $field,
                    'is_active' => true,
                ]
            );
        }
    }

    private function coin(
        string $id,
        float $marketCap,
        float $volume,
        float $change24h,
        float $change7d,
        float $change30d,
        ?float $volatility,
    ): CryptoCoin {
        return CryptoCoin::create([
            'coingecko_id' => $id,
            'symbol' => substr($id, 0, 6),
            'name' => ucfirst($id),
            'current_price' => 10,
            'market_cap' => $marketCap,
            'market_cap_rank' => 1,
            'total_volume' => $volume,
            'price_change_percentage_24h' => $change24h,
            'price_change_percentage_7d_in_currency' => $change7d,
            'price_change_percentage_30d_in_currency' => $change30d,
            'volatility' => $volatility,
            'is_active' => true,
            'is_stablecoin' => false,
        ]);
    }
}
