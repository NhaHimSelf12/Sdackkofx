<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Signal extends Model
{
    protected $fillable = [
        'market_id', 'strategy', 'direction', 'timeframe',
        'entry', 'stop_loss', 'take_profit', 'tp1', 'tp2', 'risk_reward',
        'confidence', 'status', 'is_primary', 'data_source', 'data_status', 'feed_price',
        'generated_at', 'expires_at', 'note',
    ];

    protected $casts = [
        'entry' => 'float',
        'stop_loss' => 'float',
        'take_profit' => 'float',
        'tp1' => 'float',
        'tp2' => 'float',
        'risk_reward' => 'float',
        'feed_price' => 'float',
        'is_primary' => 'boolean',
        'generated_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }
}
