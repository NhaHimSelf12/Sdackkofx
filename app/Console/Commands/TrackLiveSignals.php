<?php

namespace App\Console\Commands;

use App\Models\Signal;
use App\Services\TelegramService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class TrackLiveSignals extends Command
{
    protected $signature = 'forex:track-signals';
    protected $description = 'Track active signals against live market prices and trigger Telegram alerts.';

    public function handle(TelegramService $telegram, \App\Services\MarketDataService $dataService): int
    {
        // Get all active and non-closed signals
        $signals = Signal::with('market')
            ->where('status', 'active')
            ->where('is_closed', false)
            ->get();

        if ($signals->isEmpty()) {
            $this->info('No active signals to track.');
            return self::SUCCESS;
        }

        // Keep track of updated markets to avoid fetching multiple times
        $updatedMarkets = [];

        foreach ($signals as $signal) {
            $market = $signal->market;
            if (!$market) continue;

            if (!in_array($market->id, $updatedMarkets)) {
                try {
                    // Fetch latest M1 candles to update the market price in database
                    $dataService->candles($market, 'M1', 2, true);
                    $market->refresh();
                } catch (\Exception $e) {
                    Log::warning("Failed to update price for {$market->symbol}: " . $e->getMessage());
                }
                $updatedMarkets[] = $market->id;
            }

            $currentPrice = $market->price;
            if (!$currentPrice) continue;

            $isBuy = strtolower($signal->direction) === 'buy';
            $dirty = false;

            // 1. Check if Entry is hit
            if (!$signal->hit_entry) {
                $hitEntry = $isBuy ? ($currentPrice <= $signal->entry) : ($currentPrice >= $signal->entry);
                if ($hitEntry) {
                    $signal->hit_entry = true;
                    $dirty = true;
                    // Only alert Telegram for XAUUSD as requested previously
                    if (strtoupper($market->symbol) === 'XAUUSD') {
                        $telegram->sendStatusAlert($signal, 'active');
                    }
                }
            }

            // 2. If Entry is already hit, check for TP/SL
            if ($signal->hit_entry && !$signal->is_closed) {
                
                // Check SL
                $hitSL = $isBuy ? ($currentPrice <= $signal->stop_loss) : ($currentPrice >= $signal->stop_loss);
                if ($hitSL && !$signal->hit_sl) {
                    $signal->hit_sl = true;
                    $signal->is_closed = true;
                    $dirty = true;
                    if (strtoupper($market->symbol) === 'XAUUSD') {
                        $telegram->sendStatusAlert($signal, 'sl');
                    }
                    goto save_signal; // skip TP checks if SL is hit
                }

                // Check Full TP
                if ($signal->take_profit) {
                    $hitTP = $isBuy ? ($currentPrice >= $signal->take_profit) : ($currentPrice <= $signal->take_profit);
                    if ($hitTP && !$signal->hit_tp) {
                        $signal->hit_tp = true;
                        $signal->hit_tp2 = true; // Assume TP2 is hit if Full TP is hit
                        $signal->hit_tp1 = true;
                        $signal->is_closed = true;
                        $dirty = true;
                        if (strtoupper($market->symbol) === 'XAUUSD') {
                            $telegram->sendStatusAlert($signal, 'tp');
                        }
                        goto save_signal;
                    }
                }

                // Check TP2
                if ($signal->tp2) {
                    $hitTP2 = $isBuy ? ($currentPrice >= $signal->tp2) : ($currentPrice <= $signal->tp2);
                    if ($hitTP2 && !$signal->hit_tp2) {
                        $signal->hit_tp2 = true;
                        $signal->hit_tp1 = true; // Assume TP1 is hit if TP2 is hit
                        $dirty = true;
                        if (strtoupper($market->symbol) === 'XAUUSD') {
                            $telegram->sendStatusAlert($signal, 'tp2');
                        }
                    }
                }

                // Check TP1
                if ($signal->tp1) {
                    $hitTP1 = $isBuy ? ($currentPrice >= $signal->tp1) : ($currentPrice <= $signal->tp1);
                    if ($hitTP1 && !$signal->hit_tp1) {
                        $signal->hit_tp1 = true;
                        $dirty = true;
                        if (strtoupper($market->symbol) === 'XAUUSD') {
                            $telegram->sendStatusAlert($signal, 'tp1');
                        }
                    }
                }
            }

            save_signal:
            if ($dirty) {
                $signal->save();
                $this->info("Updated tracking for {$market->symbol} Signal #{$signal->id}");
            }
        }

        return self::SUCCESS;
    }
}
