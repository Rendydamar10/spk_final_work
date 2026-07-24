<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saw_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('crypto_coin_id')->constrained('crypto_coins')->cascadeOnDelete();
            $table->decimal('score', 16, 10)->default(0);
            $table->unsignedInteger('rank')->nullable();
            $table->json('normalized_values')->nullable();
            $table->json('weighted_values')->nullable();
            $table->timestamp('calculated_at')->nullable();
            $table->timestamps();

            $table->unique('crypto_coin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saw_results');
    }
};
