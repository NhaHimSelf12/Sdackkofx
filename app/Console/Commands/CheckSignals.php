<?php

namespace App\Console\Commands;

use App\Models\Signal;
use Illuminate\Console\Command;

class CheckSignals extends Command
{
    protected $signature = 'forex:signal-check {symbol?}';
    protected $description = 'Show active signals with market-feed provenance and expiry.';

    public function handle(): int
    {
        $signals = Signal::with('market')->where('status', 'active')
            ->when($this->argument('symbol'), fn($q) => $q->whereHas('market', fn($m) => $m->where('symbol', strtoupper($this->argument('symbol')))))
            ->orderByDesc('confidence')->get();
        $this->table(['Market','Strategy','Side','Entry','SL','TP','Conf','Feed','Generated','Expires'], $signals->map(fn($s)=>[
            $s->market->symbol,$s->strategy,strtoupper($s->direction),$s->entry,$s->stop_loss,$s->take_profit,$s->confidence.'%',
            strtoupper(($s->data_status?:'unknown').'/'.($s->data_source?:'unknown')),
            optional($s->generated_at)->toDateTimeString(),optional($s->expires_at)->toDateTimeString(),
        ]));
        if ($signals->isEmpty()) $this->warn('No active setup. Run: php artisan forex:scan');
        return self::SUCCESS;
    }
}
