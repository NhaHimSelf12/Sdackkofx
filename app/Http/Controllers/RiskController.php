<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Services\RiskCalculatorService;
use Illuminate\Http\Request;

class RiskController extends Controller
{
    public function index(Request $request)
    {
        return view('risk.index', ['markets' => Market::orderBy('symbol')->get(), 'result' => null]);
    }

    public function calculate(Request $request, RiskCalculatorService $calculator)
    {
        $data = $request->validate([
            'market_id' => ['required', 'exists:markets,id'], 'balance' => ['required', 'numeric', 'gt:0'],
            'risk_pct' => ['required', 'numeric', 'min:0.1', 'max:10'], 'entry' => ['required', 'numeric', 'gt:0'],
            'stop_loss' => ['required', 'numeric', 'gt:0'],
        ]);
        $market = Market::findOrFail($data['market_id']);
        $result = $calculator->calculate($market, $data['balance'], $data['risk_pct'], $data['entry'], $data['stop_loss']);
        return view('risk.index', ['markets' => Market::orderBy('symbol')->get(), 'result' => $result, 'market' => $market]);
    }
}
