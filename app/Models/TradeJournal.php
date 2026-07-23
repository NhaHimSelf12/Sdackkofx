<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TradeJournal extends Model
{
    protected $fillable = [
        'user_id', 'market_id', 'signal_id', 'direction', 'strategy', 'timeframe',
        'entry', 'stop_loss', 'take_profit', 'exit_price', 'lot_size', 'risk_amount',
        'profit_loss', 'r_multiple', 'status', 'emotion_before', 'execution_score',
        'setup_notes', 'review_notes', 'opened_at', 'closed_at',
    ];

    protected $casts = [
        'entry' => 'float', 'stop_loss' => 'float', 'take_profit' => 'float',
        'exit_price' => 'float', 'lot_size' => 'float', 'risk_amount' => 'float',
        'profit_loss' => 'float', 'r_multiple' => 'float',
        'opened_at' => 'datetime', 'closed_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function market() { return $this->belongsTo(Market::class); }
    public function signal() { return $this->belongsTo(Signal::class); }
}
