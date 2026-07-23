<?php

namespace App\Http\Controllers;

use App\Models\Signal;
use Illuminate\Http\Request;

class SignalController extends Controller
{
    public function index(Request $request)
    {
        Signal::where('status', 'active')->whereNotNull('expires_at')->where('expires_at', '<=', now())->update(['status' => 'expired']);
        $signals = Signal::with('market')
            ->when($request->query('direction'), fn ($q, $d) => $q->where('direction', $d))
            ->when($request->query('strategy'), fn ($q, $s) => $q->where('strategy', strtoupper($s)))
            ->when($request->query('status'), fn ($q, $s) => $q->where('status', $s), fn ($q) => $q->where('status', 'active'))
            ->orderByDesc('is_primary')
            ->orderByDesc('confidence')
            ->paginate(20)
            ->withQueryString();

        return view('signals.index', compact('signals'));
    }
}
