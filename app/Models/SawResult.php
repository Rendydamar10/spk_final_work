<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SawResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'ranking_set_id',
        'crypto_coin_id',
        'score',
        'rank',
        'raw_values',
        'normalized_values',
        'weighted_values',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'decimal:10',
        'raw_values' => 'array',
        'normalized_values' => 'array',
        'weighted_values' => 'array',
        'calculated_at' => 'datetime',
    ];

    public function scopeForRankingSet(Builder $query, int $rankingSetId): Builder
    {
        return $query->where('ranking_set_id', $rankingSetId);
    }

    public function rankingSet(): BelongsTo
    {
        return $this->belongsTo(RankingSet::class);
    }

    public function coin(): BelongsTo
    {
        return $this->belongsTo(CryptoCoin::class, 'crypto_coin_id');
    }

    public function cryptoCoin(): BelongsTo
    {
        return $this->belongsTo(CryptoCoin::class);
    }
}
