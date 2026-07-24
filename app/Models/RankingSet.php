<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RankingSet extends Model
{
    use HasFactory;

    public const SCOPE_GLOBAL = 'GLOBAL';
    public const SCOPE_USER = 'USER';

    protected $fillable = [
        'scope',
        'user_id',
        'name',
        'created_by',
    ];

    public function scopeGlobal(Builder $query): Builder
    {
        return $query->where('scope', self::SCOPE_GLOBAL)->whereNull('user_id');
    }

    public function scopeForUser(Builder $query, int $userId): Builder
    {
        return $query->where('scope', self::SCOPE_USER)->where('user_id', $userId);
    }

    public static function globalSet(?int $createdBy = null): self
    {
        return self::query()->firstOrCreate(
            [
                'scope' => self::SCOPE_GLOBAL,
                'user_id' => null,
            ],
            [
                'name' => 'Ranking Global Admin',
                'created_by' => $createdBy,
            ]
        );
    }

    public static function userSet(int $userId): self
    {
        return self::query()->firstOrCreate(
            [
                'scope' => self::SCOPE_USER,
                'user_id' => $userId,
            ],
            [
                'name' => 'Ranking Pribadi User',
                'created_by' => $userId,
            ]
        );
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function coins(): BelongsToMany
    {
        return $this->belongsToMany(CryptoCoin::class, 'ranking_set_coins')
            ->withTimestamps();
    }

    public function results(): HasMany
    {
        return $this->hasMany(SawResult::class);
    }
}
