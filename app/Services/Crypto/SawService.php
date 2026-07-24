<?php

namespace App\Services\Crypto;

use App\Models\Criterion;
use App\Models\RankingSet;
use App\Models\SawResult;
use DomainException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SawService
{
    public function calculate(): Collection
    {
        return $this->calculateForRankingSet(RankingSet::globalSet());
    }

    public function calculateForRankingSet(RankingSet $rankingSet): Collection
    {
        $criteria = $this->activeCriteria();
        $coins = $rankingSet->coins()
            ->where('crypto_coins.is_active', true)
            ->get();

        $ranked = $this->calculateRows($coins, $criteria);

        if ($ranked->isEmpty()) {
            SawResult::where('ranking_set_id', $rankingSet->id)->delete();

            return collect();
        }

        DB::transaction(function () use ($rankingSet, $ranked) {
            $rankedCoinIds = $ranked->pluck('coin.id')->all();

            SawResult::where('ranking_set_id', $rankingSet->id)
                ->whereNotIn('crypto_coin_id', $rankedCoinIds)
                ->delete();

            foreach ($ranked as $index => $row) {
                SawResult::updateOrCreate(
                    [
                        'ranking_set_id' => $rankingSet->id,
                        'crypto_coin_id' => $row['coin']->id,
                    ],
                    [
                        'score' => $row['score'],
                        'rank' => $index + 1,
                        'raw_values' => $row['raw_values'],
                        'normalized_values' => $row['normalized_values'],
                        'weighted_values' => $row['weighted_values'],
                        'calculated_at' => now(),
                    ]
                );
            }
        });

        return $ranked;
    }

    /**
     * Menghitung SAW sementara untuk sekumpulan coin tanpa menyimpan hasilnya.
     * Dipakai halaman Perbandingan Coin dan tidak bergantung pada Ranking Saya.
     */
    public function calculateForCoins(Collection $coins): Collection
    {
        return $this->calculateRows($coins, $this->activeCriteria());
    }

    private function activeCriteria(): Collection
    {
        $criteria = Criterion::query()
            ->where('is_active', true)
            ->ordered()
            ->get();

        $this->assertCriteriaAreValid($criteria);

        return $criteria;
    }

    private function calculateRows(Collection $coins, Collection $criteria): Collection
    {
        $coins = $coins
            ->filter(fn ($coin) => (bool) $coin->is_active)
            ->filter(fn ($coin) => $this->hasCompleteData($coin, $criteria))
            ->values();

        if ($coins->isEmpty()) {
            return collect();
        }

        $ranges = $this->buildRanges($coins, $criteria);
        $rows = collect();

        foreach ($coins as $coin) {
            $raw = [];
            $normalized = [];
            $weighted = [];
            $score = 0.0;

            foreach ($criteria as $criterion) {
                $field = $criterion->source_field;
                $value = (float) $coin->{$field};
                $range = $ranges[$field];
                $normalValue = $this->normalize(
                    $value,
                    $range['min'],
                    $range['max'],
                    $criterion->type
                );
                $weight = (float) $criterion->weight;
                $weightedValue = $normalValue * $weight;

                $raw[$criterion->code] = round($value, 8);
                $normalized[$criterion->code] = round($normalValue, 8);
                $weighted[$criterion->code] = round($weightedValue, 8);
                $score += $weightedValue;
            }

            $rows->push([
                'coin' => $coin,
                'score' => round($score, 10),
                'raw_values' => $raw,
                'normalized_values' => $normalized,
                'weighted_values' => $weighted,
            ]);
        }

        return $rows
            ->sort(function (array $left, array $right): int {
                $scoreComparison = $right['score'] <=> $left['score'];

                if ($scoreComparison !== 0) {
                    return $scoreComparison;
                }

                $marketCapComparison = (float) $right['coin']->market_cap <=> (float) $left['coin']->market_cap;

                if ($marketCapComparison !== 0) {
                    return $marketCapComparison;
                }

                return strcmp($left['coin']->coingecko_id, $right['coin']->coingecko_id);
            })
            ->values()
            ->map(function (array $row, int $index): array {
                $row['rank'] = $index + 1;

                return $row;
            });
    }

    private function assertCriteriaAreValid(Collection $criteria): void
    {
        if ($criteria->isEmpty()) {
            throw new DomainException('Tidak ada kriteria SAW aktif.');
        }

        $allowedTypes = ['benefit', 'cost'];
        $codes = $criteria->pluck('code');
        $fields = $criteria->pluck('source_field');

        if ($codes->duplicates()->isNotEmpty()) {
            throw new DomainException('Kode kriteria aktif harus unik.');
        }

        if ($fields->contains(fn ($field) => blank($field))) {
            throw new DomainException('Setiap kriteria aktif wajib memiliki source_field.');
        }

        foreach ($criteria as $criterion) {
            if (!in_array($criterion->type, $allowedTypes, true)) {
                throw new DomainException('Tipe kriteria harus benefit atau cost.');
            }

            $weight = (float) $criterion->weight;
            if (!is_finite($weight) || $weight < 0 || $weight > 1) {
                throw new DomainException('Bobot setiap kriteria harus berada pada rentang 0 sampai 1.');
            }
        }

        $totalWeight = $criteria->sum(fn ($criterion) => (float) $criterion->weight);

        if (abs($totalWeight - 1.0) > 0.0001) {
            throw new DomainException(
                'Total bobot kriteria aktif harus 1.0000. Total saat ini: '.number_format($totalWeight, 4)
            );
        }
    }

    private function hasCompleteData(object $coin, Collection $criteria): bool
    {
        foreach ($criteria as $criterion) {
            $value = $coin->{$criterion->source_field};

            if ($value === null || $value === '' || !is_numeric($value) || !is_finite((float) $value)) {
                return false;
            }
        }

        return true;
    }

    private function buildRanges(Collection $coins, Collection $criteria): array
    {
        $ranges = [];

        foreach ($criteria as $criterion) {
            $field = $criterion->source_field;
            $values = $coins->map(fn ($coin) => (float) $coin->{$field});

            $ranges[$field] = [
                'min' => (float) $values->min(),
                'max' => (float) $values->max(),
            ];
        }

        return $ranges;
    }

    private function normalize(float $value, float $min, float $max, string $type): float
    {
        if (abs($max - $min) < PHP_FLOAT_EPSILON) {
            return 1.0;
        }

        if ($type === 'benefit') {
            return max(0.0, min(1.0, ($value - $min) / ($max - $min)));
        }

        return max(0.0, min(1.0, ($max - $value) / ($max - $min)));
    }
}
