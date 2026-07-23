<?php

namespace App\Http\Controllers;

use App\Models\EaBot;
use App\Models\EaBotTrade;
use App\Models\Market;
use App\Services\EaBotEngine;
use Illuminate\Http\Request;

class EaBotController extends Controller
{
    private function guard(Request $request): void
    {
        abort_unless($request->user()?->isAdmin(), 403);
    }

    public function index(Request $request, EaBotEngine $engine)
    {
        $this->guard($request);

        // Keep paper positions fresh whenever an admin opens the page.
        $engine->runAll();

        return view('admin.ea-bots', [
            'modes' => EaBotEngine::MODES,
            'bots' => EaBot::with('market')->withCount(['trades as open_trades_count' => fn ($q) => $q->where('status', 'open')])->latest()->get(),
            'markets' => Market::orderBy('symbol')->get(),
            'recentTrades' => EaBotTrade::with(['bot', 'market'])->latest('opened_at')->take(20)->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->guard($request);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:60'],
            'mode' => ['required', 'in:'.implode(',', array_keys(EaBotEngine::MODES))],
            'market_id' => ['nullable', 'exists:markets,id'],
            'capital' => ['required', 'numeric', 'min:'.EaBotEngine::MIN_CAPITAL, 'max:'.EaBotEngine::MAX_CAPITAL],
            'risk_pct' => ['required', 'numeric', 'min:0.25', 'max:5'],
        ]);

        EaBot::create($data + ['status' => 'running']);

        return back()->with('status', 'EA bot created. It will start taking paper entries on the next run.');
    }

    public function update(Request $request, EaBot $bot)
    {
        $this->guard($request);
        $data = $request->validate(['status' => ['required', 'in:running,paused']]);
        $bot->update($data);

        return back()->with('status', $data['status'] === 'running' ? 'Bot resumed.' : 'Bot paused.');
    }

    public function runNow(Request $request, EaBot $bot, EaBotEngine $engine)
    {
        $this->guard($request);
        $result = $engine->run($bot);

        return back()->with('status', sprintf('Ran %s: %d opened, %d closed. %s', $bot->name, $result['opened'], $result['closed'], $result['note'] ?? ''));
    }

    public function destroy(Request $request, EaBot $bot)
    {
        $this->guard($request);
        $bot->delete();

        return back()->with('status', 'Bot deleted.');
    }
}
