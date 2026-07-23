<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\TradeJournal;
use App\Services\RiskCalculatorService;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(Request $request)
    {
        $trades = $request->user()->journals()->with('market')->latest()->paginate(20);
        $closed = $request->user()->journals()->whereIn('status', ['won', 'lost', 'breakeven']);
        $total = (clone $closed)->count();
        $wins = (clone $closed)->where('status', 'won')->count();
        $stats = [
            'total' => $total,
            'win_rate' => $total ? round($wins / $total * 100, 1) : 0,
            'net_pnl' => (clone $closed)->sum('profit_loss'),
            'avg_r' => round((float) (clone $closed)->avg('r_multiple'), 2),
        ];
        return view('journal.index', compact('trades', 'stats'));
    }

    public function create()
    {
        return view('journal.create', ['markets' => Market::orderBy('symbol')->get()]);
    }

    public function store(Request $request, RiskCalculatorService $risk)
    {
        $data = $request->validate([
            'market_id' => ['required', 'exists:markets,id'], 'direction' => ['required', 'in:buy,sell'],
            'strategy' => ['nullable', 'string', 'max:30'], 'timeframe' => ['required', 'string', 'max:10'],
            'entry' => ['required', 'numeric', 'gt:0'], 'stop_loss' => ['required', 'numeric', 'gt:0'],
            'take_profit' => ['nullable', 'numeric', 'gt:0'], 'risk_pct' => ['required', 'numeric', 'min:0.1', 'max:10'],
            'setup_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $market = Market::findOrFail($data['market_id']);
        $calc = $risk->calculate($market, $request->user()->account_balance, $data['risk_pct'], $data['entry'], $data['stop_loss']);
        $request->user()->journals()->create([
            ...collect($data)->except('risk_pct')->all(),
            'lot_size' => $calc['lot_size'], 'risk_amount' => $calc['risk_amount'], 'status' => 'planned',
        ]);
        return redirect()->route('journal.index')->with('success', 'Trade plan saved successfully.');
    }

    public function update(Request $request, TradeJournal $journal)
    {
        abort_unless($journal->user_id === $request->user()->id, 403);
        $data = $request->validate([
            'status' => ['required', 'in:planned,open,won,lost,breakeven,cancelled'],
            'exit_price' => ['nullable', 'numeric'], 'profit_loss' => ['nullable', 'numeric'],
            'r_multiple' => ['nullable', 'numeric'], 'execution_score' => ['nullable', 'integer', 'between:1,10'],
            'review_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $journal->update([...$data, 'closed_at' => in_array($data['status'], ['won','lost','breakeven']) ? now() : null]);
        return back()->with('success', 'Journal updated successfully.');
    }

    public function destroy(Request $request, TradeJournal $journal)
    {
        abort_unless($journal->user_id === $request->user()->id, 403);
        $journal->delete();
        return back();
    }
}
