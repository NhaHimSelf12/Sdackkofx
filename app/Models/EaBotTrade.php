<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EaBotTrade extends Model
{
    protected $fillable = [
        'ea_bot_id', 'market_id', 'signal_id', 'direction',
        'entry', 'stop_loss', 'take_profit', 'units', 'risk_amount',
        'status', 'pnl', 'note', 'opened_at', 'closed_at',
    ];

    protected $casts = [
        'entry' => 'float',
        'stop_loss' => 'float',
        'take_profit' => 'float',
        'units' => 'float',
        'risk_amount' => 'float',
        'pnl' => 'float',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function bot(): BelongsTo
    {
        return $this->belongsTo(EaBot::class, 'ea_bot_id');
    }

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }

    public function signal(): BelongsTo
    {
        return $this->belongsTo(Signal::class);
    }
}
