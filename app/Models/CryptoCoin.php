<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CryptoCoin extends Model
{
    use HasFactory;

    protected $fillable = [
        'coingecko_id',
        'symbol',
        'name',
        'image',
        'logo_path',
        'logo_source_url',
        'current_price',
        'market_cap',
        'market_cap_rank',
        'total_volume',
        'price_change_percentage_24h',
        'price_change_percentage_7d_in_currency',
        'price_change_percentage_30d_in_currency',
        'volatility',
        'source_api',
        'is_stablecoin',
        'is_active',
        'last_synced_at',
        'raw_data',
    ];

    protected $casts = [
        'current_price' => 'decimal:10',
        'market_cap' => 'decimal:2',
        'total_volume' => 'decimal:2',
        'price_change_percentage_24h' => 'decimal:8',
        'price_change_percentage_7d_in_currency' => 'decimal:8',
        'price_change_percentage_30d_in_currency' => 'decimal:8',
        'volatility' => 'decimal:8',
        'is_stablecoin' => 'boolean',
        'is_active' => 'boolean',
        'last_synced_at' => 'datetime',
        'raw_data' => 'array',
    ];


    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        return '/storage/'.ltrim($this->logo_path, '/');
    }

    public function sawResult(): HasOne
    {
        return $this->hasOne(SawResult::class);
    }

    public function globalSawResult(): HasOne
    {
        return $this->hasOne(SawResult::class)
            ->whereHas('rankingSet', fn ($query) => $query->global());
    }

    public function sawResults(): HasMany
    {
        return $this->hasMany(SawResult::class);
    }

    public function rankingSets(): BelongsToMany
    {
        return $this->belongsToMany(RankingSet::class, 'ranking_set_coins')
            ->withTimestamps();
    }

    public function watchlists(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}
