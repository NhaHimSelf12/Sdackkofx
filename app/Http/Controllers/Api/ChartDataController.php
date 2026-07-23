<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Market;
use App\Services\MarketDataService;
use Illuminate\Http\Request;

class ChartDataController extends Controller
{
    public function candles(Request $request, Market $market, MarketDataService $marketData)
    {
        $timeframe = $request->query('timeframe', config('forex.default_timeframe', 'H1'));

        return response()->json($marketData->candles($market, $timeframe));
    }

    public function trendlines(Request $request, Market $market)
    {
        $timeframe = $request->query('timeframe', config('forex.default_timeframe', 'H1'));

        return response()->json(
            $market->trendlines()->where('timeframe', $timeframe)->get(),
        );
    }

    public function analysis(Market $market)
    {
        return response()->json([
            'symbol' => $market->symbol,
            'bias' => $market->ai_bias,
            'confidence' => $market->ai_confidence,
            'summary' => $market->ai_summary,
            'key_levels' => $market->key_levels,
            'analyzed_at' => $market->analyzed_at,
        ]);
    }
}
