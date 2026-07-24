<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crypto_coins', function (Blueprint $table) {
            $table->id();
            $table->string('coingecko_id')->unique();
            $table->string('symbol', 30)->index();
            $table->string('name');
            $table->text('image')->nullable();
            $table->decimal('current_price', 28, 10)->nullable();
            $table->decimal('market_cap', 32, 2)->nullable();
            $table->unsignedInteger('market_cap_rank')->nullable();
            $table->decimal('total_volume', 32, 2)->nullable();
            $table->decimal('price_change_percentage_24h', 16, 8)->nullable();
            $table->decimal('price_change_percentage_7d_in_currency', 16, 8)->nullable();
            $table->decimal('price_change_percentage_30d_in_currency', 16, 8)->nullable();
            $table->decimal('volatility', 16, 8)->nullable();
            $table->string('source_api')->default('coingecko');
            $table->boolean('is_stablecoin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_synced_at')->nullable();
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crypto_coins');
    }
};
