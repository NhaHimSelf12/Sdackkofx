<?php

namespace App\Http\Controllers;

use App\Models\Market;

class MarketController extends Controller
{
    public function index()
    {
        $markets = Market::withCount(['signals as active_signals_count' => fn ($q) => $q->where('status', 'active')])
            ->orderBy('category')
            ->orderBy('symbol')
            ->get()
            ->groupBy('category');

        return view('markets.index', compact('markets'));
    }

    public function show(Market $market)
    {
        $market->load(['trendlines', 'signals' => fn ($q) => $q->latest()->take(10)]);

        return view('markets.show', compact('market'));
    }
}
