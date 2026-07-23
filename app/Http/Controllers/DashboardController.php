<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\NewsItem;
use App\Models\Signal;

class DashboardController extends Controller
{
    public function index()
    {
        Signal::where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', now())->update(['status' => 'expired']);
        $markets = Market::orderBy('symbol')->get();
        $signals = Signal::with('market')
            ->where('status', 'active')
            ->orderByDesc('is_primary')
            ->orderByDesc('confidence')
            ->take(8)
            ->get();
        $news = NewsItem::orderByDesc('published_at')->take(6)->get();

        $stats = [
            'markets' => $markets->count(),
            'active_signals' => Signal::where('status', 'active')->count(),
            'buy_signals' => Signal::where('status', 'active')->where('direction', 'buy')->count(),
            'sell_signals' => Signal::where('status', 'active')->where('direction', 'sell')->count(),
            'bullish_markets' => $markets->where('ai_bias', 'bullish')->count(),
            'bearish_markets' => $markets->where('ai_bias', 'bearish')->count(),
        ];

        return view('dashboard', compact('markets', 'signals', 'news', 'stats'));
    }
}
