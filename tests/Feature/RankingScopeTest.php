<?php

namespace Tests\Feature;

use App\Models\Criterion;
use App\Models\CryptoCoin;
use App\Models\RankingSet;
use App\Models\SawResult;
use App\Models\User;
use App\Services\Crypto\SawService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as HttpRequest;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class RankingScopeTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_refresh_global_ranking_and_user_cannot(): void
    {
        $this->seedCriteria();
        $this->fakeCoinGecko($this->marketPayload(12));

        $admin = User::factory()->create(['role' => 'admin']);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($admin)
            ->post(route('admin.refresh.run'))
            ->assertRedirect()
            ->assertSessionHas('success');

        $globalSet = RankingSet::global()->firstOrFail();

        $this->assertSame(10, $globalSet->coins()->count());
        $this->assertSame(10, SawResult::where('ranking_set_id', $globalSet->id)->count());

        $this->actingAs($user)
            ->post(route('admin.refresh.run'))
            ->assertForbidden();
    }

    public function test_user_coin_does_not_appear_on_global_dashboard(): void
    {
        $this->seedCriteria();

        $user = User::factory()->create(['role' => 'user']);
        $globalCoin = $this->coin('bitcoin', 'btc', 'Bitcoin', 1);
        $userCoin = $this->coin('chainlink', 'link', 'Chainlink', 20);

        $globalSet = RankingSet::globalSet();
        $globalSet->coins()->sync([$globalCoin->id]);
        RankingSet::userSet($user->id)->coins()->sync([$userCoin->id]);

        app(SawService::class)->calculateForRankingSet($globalSet);
        app(SawService::class)->calculateForRankingSet(RankingSet::userSet($user->id));

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Bitcoin')
            ->assertDontSee('Chainlink');
    }

    public function test_user_ranking_is_isolated_from_other_users_and_global(): void
    {
        $this->seedCriteria();
        $this->fakeCoinGecko([$this->marketItem('polkadot', 'dot', 'Polkadot', 40)]);

        $userA = User::factory()->create(['role' => 'user']);
        $userB = User::factory()->create(['role' => 'user']);
        $globalCoin = $this->coin('bitcoin', 'btc', 'Bitcoin', 1);

        $globalSet = RankingSet::globalSet();
        $globalSet->coins()->sync([$globalCoin->id]);
        app(SawService::class)->calculateForRankingSet($globalSet);

        $this->actingAs($userA)
            ->post(route('user.ranking.coins.store'), ['coingecko_id' => 'polkadot'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $userASet = RankingSet::forUser($userA->id)->firstOrFail();
        $userBSet = RankingSet::userSet($userB->id);

        $this->assertSame(1, $userASet->coins()->count());
        $this->assertSame(0, $userBSet->coins()->count());
        $this->assertSame(1, $globalSet->fresh()->coins()->count());
        $this->assertDatabaseMissing('ranking_set_coins', [
            'ranking_set_id' => $globalSet->id,
            'crypto_coin_id' => CryptoCoin::where('coingecko_id', 'polkadot')->firstOrFail()->id,
        ]);
    }

    public function test_watchlist_is_independent_from_personal_ranking(): void
    {
        $this->seedCriteria();
        $this->fakeCoinGecko([$this->marketItem('solana', 'sol', 'Solana', 5)]);
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user)
            ->post(route('user.watchlist.store'), ['coingecko_id' => 'solana'])
            ->assertRedirect()
            ->assertSessionHas('success');

        $coin = CryptoCoin::where('coingecko_id', 'solana')->firstOrFail();
        $rankingSet = RankingSet::userSet($user->id);

        $this->assertDatabaseHas('watchlists', [
            'user_id' => $user->id,
            'crypto_coin_id' => $coin->id,
        ]);
        $this->assertFalse($rankingSet->coins()->whereKey($coin->id)->exists());
    }

    public function test_user_cannot_delete_coin_from_another_users_ranking(): void
    {
        $this->seedCriteria();

        $userA = User::factory()->create(['role' => 'user']);
        $userB = User::factory()->create(['role' => 'user']);
        $coin = $this->coin('litecoin', 'ltc', 'Litecoin', 30);

        RankingSet::userSet($userB->id)->coins()->sync([$coin->id]);
        app(SawService::class)->calculateForRankingSet(RankingSet::userSet($userB->id));

        $this->actingAs($userA)
            ->delete(route('user.ranking.coins.destroy', $coin))
            ->assertNotFound();

        $this->assertTrue(RankingSet::userSet($userB->id)->coins()->whereKey($coin->id)->exists());
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

    private function fakeCoinGecko(array $markets): void
    {
        Http::fake(function (HttpRequest $request) use ($markets) {
            if (str_contains($request->url(), '/market_chart')) {
                return Http::response(['prices' => $this->priceHistory()], 200);
            }

            return Http::response($markets, 200);
        });
    }

    private function priceHistory(): array
    {
        $start = strtotime('2026-01-01 UTC') * 1000;

        return collect(range(0, 30))
            ->map(fn (int $day) => [$start + ($day * 86400000), 100 + ($day * 1.2) + (($day % 3) - 1)])
            ->all();
    }

    private function marketPayload(int $count): array
    {
        return collect(range(1, $count))
            ->map(fn (int $number) => $this->marketItem("coin-{$number}", "c{$number}", "Coin {$number}", $number))
            ->all();
    }

    private function marketItem(string $id, string $symbol, string $name, int $rank): array
    {
        return [
            'id' => $id,
            'symbol' => $symbol,
            'name' => $name,
            'image' => null,
            'current_price' => 1000 / $rank,
            'market_cap' => 1000000 / $rank,
            'market_cap_rank' => $rank,
            'total_volume' => 500000 / $rank,
            'price_change_percentage_24h' => 1.5,
            'price_change_percentage_7d_in_currency' => 2.5,
            'price_change_percentage_30d_in_currency' => 3.5,
        ];
    }

    private function coin(string $id, string $symbol, string $name, int $rank): CryptoCoin
    {
        return CryptoCoin::create([
            'coingecko_id' => $id,
            'symbol' => $symbol,
            'name' => $name,
            'current_price' => 1000 / $rank,
            'market_cap' => 1000000 / $rank,
            'market_cap_rank' => $rank,
            'total_volume' => 500000 / $rank,
            'price_change_percentage_24h' => 1.5,
            'price_change_percentage_7d_in_currency' => 2.5,
            'price_change_percentage_30d_in_currency' => 3.5,
            'volatility' => 2.0,
            'is_active' => true,
            'is_stablecoin' => false,
        ]);
    }
}
