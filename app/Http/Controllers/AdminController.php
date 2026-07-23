<?php

namespace App\Http\Controllers;

use App\Models\Market;
use App\Models\NewsItem;
use App\Models\Signal;
use App\Models\TradeJournal;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function guard(Request $request): void { abort_unless($request->user()?->isAdmin(), 403); }

    public function index(Request $request)
    {
        $this->guard($request);
        return view('admin.index', [
            'stats' => ['users' => User::count(), 'markets' => Market::count(), 'signals' => Signal::count(), 'trades' => TradeJournal::count(), 'news' => NewsItem::count()],
            'users' => User::latest()->take(10)->get(),
            'signals' => Signal::with('market')->latest()->take(10)->get(),
        ]);
    }

    public function userRole(Request $request, User $user)
    {
        $this->guard($request);
        $data = $request->validate(['role' => ['required', 'in:trader,admin']]);
        $user->update($data);
        return back();
    }
}
