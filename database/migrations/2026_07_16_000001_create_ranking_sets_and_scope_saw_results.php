<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ranking_sets')) {
            Schema::create('ranking_sets', function (Blueprint $table) {
                $table->id();
                $table->string('scope', 20)->index();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->string('name');
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['scope', 'user_id']);
            });
        }

        if (!Schema::hasTable('ranking_set_coins')) {
            Schema::create('ranking_set_coins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ranking_set_id')->constrained('ranking_sets')->cascadeOnDelete();
                $table->foreignId('crypto_coin_id')->constrained('crypto_coins')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['ranking_set_id', 'crypto_coin_id']);
            });
        }

        $globalRankingSet = DB::table('ranking_sets')
            ->where('scope', 'GLOBAL')
            ->whereNull('user_id')
            ->first();

        $globalRankingSetId = $globalRankingSet?->id ?? DB::table('ranking_sets')->insertGetId([
            'scope' => 'GLOBAL',
            'user_id' => null,
            'name' => 'Ranking Global Admin',
            'created_by' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (!$this->indexExists('saw_results', 'saw_results_crypto_coin_id_index')) {
            Schema::table('saw_results', function (Blueprint $table) {
                $table->index('crypto_coin_id', 'saw_results_crypto_coin_id_index');
            });
        }

        if ($this->indexExists('saw_results', 'saw_results_crypto_coin_id_unique')) {
            Schema::table('saw_results', function (Blueprint $table) {
                $table->dropUnique('saw_results_crypto_coin_id_unique');
            });
        }

        if (!Schema::hasColumn('saw_results', 'ranking_set_id')) {
            Schema::table('saw_results', function (Blueprint $table) {
                $table->foreignId('ranking_set_id')
                    ->nullable()
                    ->after('id')
                    ->constrained('ranking_sets')
                    ->cascadeOnDelete();
            });
        } elseif (!$this->foreignKeyExists('saw_results', 'saw_results_ranking_set_id_foreign')) {
            Schema::table('saw_results', function (Blueprint $table) {
                $table->foreign('ranking_set_id')
                    ->references('id')
                    ->on('ranking_sets')
                    ->cascadeOnDelete();
            });
        }

        DB::table('saw_results')->update([
            'ranking_set_id' => $globalRankingSetId,
            'updated_at' => now(),
        ]);

        $existingCoinIds = DB::table('saw_results')
            ->whereNotNull('crypto_coin_id')
            ->distinct()
            ->pluck('crypto_coin_id');

        foreach ($existingCoinIds as $coinId) {
            DB::table('ranking_set_coins')->updateOrInsert(
                [
                    'ranking_set_id' => $globalRankingSetId,
                    'crypto_coin_id' => $coinId,
                ],
                [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        Schema::table('saw_results', function (Blueprint $table) {
            if (!$this->indexExists('saw_results', 'saw_results_ranking_set_id_crypto_coin_id_unique')) {
                $table->unique(['ranking_set_id', 'crypto_coin_id']);
            }

            if (!$this->indexExists('saw_results', 'saw_results_ranking_set_id_rank_index')) {
                $table->index(['ranking_set_id', 'rank']);
            }
        });
    }

    public function down(): void
    {
        Schema::table('saw_results', function (Blueprint $table) {
            if ($this->indexExists('saw_results', 'saw_results_ranking_set_id_rank_index')) {
                $table->dropIndex(['ranking_set_id', 'rank']);
            }

            if ($this->indexExists('saw_results', 'saw_results_ranking_set_id_crypto_coin_id_unique')) {
                $table->dropUnique(['ranking_set_id', 'crypto_coin_id']);
            }

            if (Schema::hasColumn('saw_results', 'ranking_set_id')) {
                $table->dropConstrainedForeignId('ranking_set_id');
            }

            if (!$this->indexExists('saw_results', 'saw_results_crypto_coin_id_unique')) {
                $table->unique('crypto_coin_id');
            }

            if ($this->indexExists('saw_results', 'saw_results_crypto_coin_id_index')) {
                $table->dropIndex('saw_results_crypto_coin_id_index');
            }
        });

        Schema::dropIfExists('ranking_set_coins');
        Schema::dropIfExists('ranking_sets');
    }

    private function indexExists(string $table, string $index): bool
    {
        return !empty(DB::select('SHOW INDEX FROM `'.$table.'` WHERE Key_name = ?', [$index]));
    }

    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        return !empty(DB::select(
            'SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?',
            [$table, $foreignKey]
        ));
    }
};
