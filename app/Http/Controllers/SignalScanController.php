<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Services\SignalEngine;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class SignalScanController extends Controller
{
    public function __invoke(Request $request, SignalEngine $engine, TelegramService $telegram)
    {
        $created = 0; $demo = []; $allSignals = collect();
        
        $symbols = $request->has('symbol') ? [$request->input('symbol')] : ['XAUUSD', 'BTCUSD', 'EURUSD'];
        $markets = Market::whereIn('symbol', $symbols)->get();

        foreach ($markets as $market) {
            $signals = $engine->scan($market, config('forex.default_timeframe', 'H1'), true);
            $created += count($signals);
            $allSignals = $allSignals->merge($signals);
            $market->refresh();
            if ($market->data_status === 'demo') $demo[] = $market->symbol;
        }

        // Filter only XAUUSD signals for Telegram
        $telegramSignals = $allSignals->filter(function ($signal) {
            return strtoupper($signal->market->symbol ?? '') === 'XAUUSD';
        });

        // Send signals to Telegram (ONLY IF ADMIN)
        if ($telegramSignals->isNotEmpty() && auth()->check() && auth()->user()->isAdmin()) {
            $telegram->sendSignals($telegramSignals);
        }

        $message = "Signal scan complete: {$created} active setups generated.";
        if ($demo) $message .= ' Skipped demo feeds: '.implode(', ', $demo).'.';
        return back()->with($demo ? 'warning' : 'success', $message);
    }
}
