<?php

namespace App\Http\Controllers;

use App\Models\Market;
use Illuminate\Http\Request;

class WatchlistController extends Controller
{
    public function toggle(Request $request, Market $market)
    {
        $exists = $request->user()->watchlistMarkets()->whereKey($market->id)->exists();
        $exists ? $request->user()->watchlistMarkets()->detach($market) : $request->user()->watchlistMarkets()->attach($market);
        return back();
    }
}
