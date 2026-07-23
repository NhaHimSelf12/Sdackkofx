<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Market extends Model
{
    protected $fillable = [
        'symbol', 'name', 'category', 'price', 'change_pct',
        'data_source', 'data_status', 'price_fetched_at', 'feed_error',
        'ai_bias', 'ai_confidence', 'ai_summary', 'analysis_details', 'key_levels', 'analyzed_at',
    ];

    protected $casts = [
        'key_levels' => 'array',
        'analysis_details' => 'array',
        'analyzed_at' => 'datetime',
        'price_fetched_at' => 'datetime',
        'price' => 'float',
        'change_pct' => 'float',
    ];

    public function signals(): HasMany
    {
        return $this->hasMany(Signal::class);
    }

    public function trendlines(): HasMany
    {
        return $this->hasMany(Trendline::class);
    }

    public function activeSignals(): HasMany
    {
        return $this->signals()->where('status', 'active');
    }

    /** Decimal places used when formatting prices for this market. */
    public function precision(): int
    {
        return match ($this->category) {
            'crypto', 'indices' => 2,
            'metals' => 2,
            default => str_contains($this->symbol, 'JPY') ? 3 : 5,
        };
    }

    /** The actual float value of 1 pip or 1 point for this market. */
    public function pipSize(): float
    {
        return match ($this->category) {
            'metals', 'crypto', 'indices' => 1.0,
            default => str_contains($this->symbol, 'JPY') ? 0.01 : 0.0001,
        };
    }
}
