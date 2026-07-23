<?php

namespace App\Http\Controllers;

use App\Domain\Strategies\StrategyRegistry;
use App\Models\Signal;

class StrategyController extends Controller
{
    public function index()
    {
        $strategies = collect(StrategyRegistry::all())->map(function ($strategy) {
            $active = Signal::where('strategy', $strategy->code())->where('status', 'active');

            return [
                'code' => $strategy->code(),
                'name' => $strategy->name(),
                'description' => $strategy->description(),
                'concepts' => $strategy->concepts(),
                'active_signals' => (clone $active)->count(),
                'buy' => (clone $active)->where('direction', 'buy')->count(),
                'sell' => (clone $active)->where('direction', 'sell')->count(),
            ];
        });

        return view('strategies.index', compact('strategies'));
    }
}
