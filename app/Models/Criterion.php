<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterion extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'weight',
        'source_field',
        'is_active',
    ];

    protected $casts = [
        'weight' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByRaw("CASE code
                WHEN 'market_cap' THEN 1
                WHEN 'total_volume' THEN 2
                WHEN 'price_change_percentage_24h' THEN 3
                WHEN 'price_change_percentage_7d_in_currency' THEN 4
                WHEN 'price_change_percentage_30d_in_currency' THEN 5
                WHEN 'volatility' THEN 6
                WHEN 'market_cap_rank' THEN 7
                ELSE 99
            END")
            ->orderBy('id');
    }
}
