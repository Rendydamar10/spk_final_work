<?php

namespace Database\Seeders;

use App\Models\Criterion;
use Illuminate\Database\Seeder;

class CriterionSeeder extends Seeder
{
    public function run(): void
    {
        $criteria = [
            [
                'code' => 'market_cap',
                'name' => 'Market Cap',
                'type' => 'benefit',
                'weight' => 0.2500,
                'source_field' => 'market_cap',
                'is_active' => true,
            ],
            [
                'code' => 'total_volume',
                'name' => 'Volume 24 Jam',
                'type' => 'benefit',
                'weight' => 0.2000,
                'source_field' => 'total_volume',
                'is_active' => true,
            ],
            [
                'code' => 'price_change_percentage_24h',
                'name' => 'Perubahan 24 Jam',
                'type' => 'benefit',
                'weight' => 0.0500,
                'source_field' => 'price_change_percentage_24h',
                'is_active' => true,
            ],
            [
                'code' => 'price_change_percentage_7d_in_currency',
                'name' => 'Perubahan 7 Hari',
                'type' => 'benefit',
                'weight' => 0.1000,
                'source_field' => 'price_change_percentage_7d_in_currency',
                'is_active' => true,
            ],
            [
                'code' => 'price_change_percentage_30d_in_currency',
                'name' => 'Perubahan 30 Hari',
                'type' => 'benefit',
                'weight' => 0.1500,
                'source_field' => 'price_change_percentage_30d_in_currency',
                'is_active' => true,
            ],
            [
                'code' => 'volatility',
                'name' => 'Volatilitas 30 Hari',
                'type' => 'cost',
                'weight' => 0.2500,
                'source_field' => 'volatility',
                'is_active' => true,
            ],
            [
                'code' => 'market_cap_rank',
                'name' => 'Market Cap Rank (Nonaktif)',
                'type' => 'cost',
                'weight' => 0.0000,
                'source_field' => 'market_cap_rank',
                'is_active' => false,
            ],
        ];

        foreach ($criteria as $criterion) {
            Criterion::updateOrCreate(
                ['code' => $criterion['code']],
                $criterion
            );
        }
    }
}

