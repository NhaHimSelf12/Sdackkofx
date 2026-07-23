<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EaBot extends Model
{
    protected $fillable = [
        'name', 'mode', 'market_id', 'capital', 'risk_pct', 'status',
        'positions_today', 'last_trade_date', 'trades', 'wins', 'losses', 'pnl', 'last_run_at', 'last_note',
    ];

    protected $casts = [
        'capital' => 'float',
        'risk_pct' => 'float',
        'pnl' => 'float',
        'last_trade_date' => 'date',
        'last_run_at' => 'datetime',
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function trades(): HasMany
    {
        return $this->hasMany(EaBotTrade::class);
    }

    public function openTrades(): HasMany
    {
        return $this->trades()->where('status', 'open');
    }

    /** Account size tier: small money -> small positions, big money -> big positions. */
    public function tier(): string
    {
        return match (true) {
            $this->capital < 100 => 'Micro',
            $this->capital < 500 => 'Mini',
            $this->capital < 2000 => 'Standard',
            default => 'Pro',
        };
    }

    public function equity(): float
    {
        return round($this->capital + $this->pnl, 2);
    }

    public function winRate(): int
    {
        return $this->trades > 0 ? (int) round($this->wins / $this->trades * 100) : 0;
    }
}
