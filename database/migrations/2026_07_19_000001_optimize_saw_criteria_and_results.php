<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('saw_results', 'raw_values')) {
            Schema::table('saw_results', function (Blueprint $table) {
                $table->json('raw_values')->nullable()->after('rank');
            });
        }

        $criteria = [
            ['code' => 'market_cap', 'name' => 'Market Cap', 'type' => 'benefit', 'weight' => 0.2500, 'source_field' => 'market_cap', 'is_active' => true],
            ['code' => 'total_volume', 'name' => 'Volume 24 Jam', 'type' => 'benefit', 'weight' => 0.2000, 'source_field' => 'total_volume', 'is_active' => true],
            ['code' => 'price_change_percentage_24h', 'name' => 'Perubahan 24 Jam', 'type' => 'benefit', 'weight' => 0.0500, 'source_field' => 'price_change_percentage_24h', 'is_active' => true],
            ['code' => 'price_change_percentage_7d_in_currency', 'name' => 'Perubahan 7 Hari', 'type' => 'benefit', 'weight' => 0.1000, 'source_field' => 'price_change_percentage_7d_in_currency', 'is_active' => true],
            ['code' => 'price_change_percentage_30d_in_currency', 'name' => 'Perubahan 30 Hari', 'type' => 'benefit', 'weight' => 0.1500, 'source_field' => 'price_change_percentage_30d_in_currency', 'is_active' => true],
            ['code' => 'volatility', 'name' => 'Volatilitas 30 Hari', 'type' => 'cost', 'weight' => 0.2500, 'source_field' => 'volatility', 'is_active' => true],
            ['code' => 'market_cap_rank', 'name' => 'Market Cap Rank (Nonaktif)', 'type' => 'cost', 'weight' => 0.0000, 'source_field' => 'market_cap_rank', 'is_active' => false],
        ];

        foreach ($criteria as $criterion) {
            DB::table('criteria')->updateOrInsert(
                ['code' => $criterion['code']],
                [...$criterion, 'updated_at' => now(), 'created_at' => now()]
            );
        }
    }

    public function down(): void
    {
        $legacyCriteria = [
            ['code' => 'market_cap', 'name' => 'Market Cap', 'type' => 'benefit', 'weight' => 0.3000, 'source_field' => 'market_cap', 'is_active' => true],
            ['code' => 'total_volume', 'name' => 'Volume 24 Jam', 'type' => 'benefit', 'weight' => 0.2500, 'source_field' => 'total_volume', 'is_active' => true],
            ['code' => 'price_change_percentage_24h', 'name' => 'Perubahan 24 Jam', 'type' => 'benefit', 'weight' => 0.1500, 'source_field' => 'price_change_percentage_24h', 'is_active' => true],
            ['code' => 'price_change_percentage_7d_in_currency', 'name' => 'Perubahan 7 Hari', 'type' => 'benefit', 'weight' => 0.1500, 'source_field' => 'price_change_percentage_7d_in_currency', 'is_active' => true],
            ['code' => 'market_cap_rank', 'name' => 'Market Cap Rank', 'type' => 'cost', 'weight' => 0.1000, 'source_field' => 'market_cap_rank', 'is_active' => true],
            ['code' => 'volatility', 'name' => 'Volatilitas', 'type' => 'cost', 'weight' => 0.0500, 'source_field' => 'volatility', 'is_active' => true],
        ];

        DB::table('criteria')->where('code', 'price_change_percentage_30d_in_currency')->delete();

        foreach ($legacyCriteria as $criterion) {
            DB::table('criteria')->updateOrInsert(
                ['code' => $criterion['code']],
                [...$criterion, 'updated_at' => now(), 'created_at' => now()]
            );
        }

        if (Schema::hasColumn('saw_results', 'raw_values')) {
            Schema::table('saw_results', function (Blueprint $table) {
                $table->dropColumn('raw_values');
            });
        }
    }
};
