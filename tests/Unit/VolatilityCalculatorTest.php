<?php

namespace Tests\Unit;

use App\Services\Crypto\VolatilityCalculator;
use PHPUnit\Framework\TestCase;

class VolatilityCalculatorTest extends TestCase
{
    public function test_it_calculates_sample_standard_deviation_of_daily_percentage_returns(): void
    {
        $calculator = new VolatilityCalculator();
        $result = $calculator->calculateFromPriceSeries([100, 110, 99, 108.9]);

        $this->assertNotNull($result);
        $this->assertEqualsWithDelta(11.54700538, $result, 0.000001);
    }

    public function test_it_uses_last_price_for_each_utc_day(): void
    {
        $calculator = new VolatilityCalculator();
        $result = $calculator->calculateFromPriceSeries([
            [1704067200000, 100],
            [1704100000000, 105],
            [1704153600000, 110],
            [1704240000000, 99],
        ]);

        $this->assertNotNull($result);
    }

    public function test_it_returns_null_when_history_is_insufficient(): void
    {
        $calculator = new VolatilityCalculator();

        $this->assertNull($calculator->calculateFromPriceSeries([100, 101]));
    }
}
