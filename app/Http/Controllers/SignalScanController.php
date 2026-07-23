<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Services\SignalEngine;
use Illuminate\Http\Request;

class SignalScanController extends Controller
{
    public function __invoke(Request $request, SignalEngine $engine)
    {
        $created = 0; $demo = [];
        foreach (Market::all() as $market) {
            $signals = $engine->scan($market, config('forex.default_timeframe', 'H1'), true);
            $created += count($signals);
            $market->refresh();
            if ($market->data_status === 'demo') $demo[] = $market->symbol;
        }
        $message = "Signal scan complete: {$created} active setups generated.";
        if ($demo) $message .= ' Skipped demo feeds: '.implode(', ', $demo).'.';
        return back()->with($demo ? 'warning' : 'success', $message);
    }
}
