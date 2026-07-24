<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\NewsItem;
use App\Models\Signal;
use App\Models\PublicStrategy;

class PublicController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'markets'=>Market::orderByDesc('change_pct')->take(8)->get(),
            'news'=>NewsItem::orderByDesc('published_at')->take(6)->get(),
            'signalCount'=>Signal::where('status','active')->count(),
            'publicStrategies'=>\App\Models\PublicStrategy::all(),
            'recentUsers'=>\App\Models\User::orderByDesc('created_at')->take(5)->get(),
            'totalUsers'=>\App\Models\User::count(),
        ]);
    }

    public function showStrategy(PublicStrategy $publicStrategy)
    {
        return view('strategy_detail', compact('publicStrategy'));
    }

    public function learn()
    {
        if (auth()->check()) {
            return redirect()->route('lessons.index');
        }
        return view('public_lesson');
    }
}
