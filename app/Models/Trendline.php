<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Trendline extends Model
{
    protected $fillable = [
        'market_id', 'kind', 'direction', 'timeframe',
        'start_time', 'start_price', 'end_time', 'end_price', 'touches',
    ];

    protected $casts = [
        'start_price' => 'float',
        'end_price' => 'float',
        'start_time' => 'integer',
        'end_time' => 'integer',
    ];

    public function market(): BelongsTo
    {
        return $this->belongsTo(Market::class);
    }
}
