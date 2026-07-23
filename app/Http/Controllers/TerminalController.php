<?php

namespace App\Http\Controllers;

use App\Models\EaBotTrade;
use App\Models\Market;
use App\Services\ChartAnalysisService;
use App\Services\MarketDataService;
use Illuminate\Http\Request;

class TerminalController extends Controller
{
    public function show(Market $market)
    {
        return view('terminal.show', ['market'=>$market,'markets'=>Market::orderBy('symbol')->get()]);
    }

    public function data(Request $request, Market $market, MarketDataService $feed, ChartAnalysisService $analysis)
    {
        $tf = in_array($request->query('timeframe'), ['M1','M5','M15','H1','H4','D1']) ? $request->query('timeframe') : 'M1';
        $candles = $feed->candles($market, $tf, 300, $request->boolean('fresh'));
        $market->refresh();
        $seconds = match($tf){'M1'=>60,'M5'=>300,'M15'=>900,'H4'=>14400,'D1'=>86400,default=>3600};
        $active = $market->signals()->where('status','active')->where(fn($q)=>$q->whereNull('expires_at')->orWhere('expires_at','>',now()))->latest()->get();
        $signals = $active->map(fn($s)=>[
            'direction'=>$s->direction,'strategy'=>$s->strategy,'entry'=>$s->entry,'stop_loss'=>$s->stop_loss,'tp1'=>$s->tp1,'tp2'=>$s->tp2,'take_profit'=>$s->take_profit,'risk_reward'=>$s->risk_reward,'confidence'=>$s->confidence,'is_primary'=>$s->is_primary,'note'=>$s->note,'generated_at'=>optional($s->generated_at)->timestamp,'expires_at'=>optional($s->expires_at)->timestamp,
        ])->values();
        $primary = $signals->firstWhere('is_primary', true);
        $botTrades = EaBotTrade::with('bot')
            ->where('market_id', $market->id)
            ->where(fn($q)=>$q->where('status','open')->orWhere('opened_at','>=',now()->subDay()))
            ->latest('opened_at')->take(12)->get()
            ->map(fn($t)=>[
                'bot'=>$t->bot?->name,'mode'=>$t->bot?->mode,'direction'=>$t->direction,
                'entry'=>$t->entry,'stop_loss'=>$t->stop_loss,'take_profit'=>$t->take_profit,
                'units'=>$t->units,'risk_amount'=>$t->risk_amount,'status'=>$t->status,'pnl'=>$t->pnl,
                'note'=>$t->note,'opened_at'=>optional($t->opened_at)->timestamp,'closed_at'=>optional($t->closed_at)->timestamp,
            ])->values();
        return response()->json([
            'symbol'=>$market->symbol,'price'=>$market->price,'change_pct'=>$market->change_pct,
            'feed'=>['status'=>$market->data_status,'source'=>$market->data_source,'fetched_at'=>optional($market->price_fetched_at)->toIso8601String(),'error'=>$market->feed_error],
            'timeframe'=>$tf,'interval_seconds'=>$seconds,'next_candle_at'=>(intdiv(time(),$seconds)+1)*$seconds,
            'candles'=>$candles,'overlays'=>$analysis->analyze($candles),'signals'=>$signals,
            'primary_signal'=>$primary,'bot_trades'=>$botTrades,
            'analysis'=>[
                'bias'=>$market->ai_bias,'confidence'=>$market->ai_confidence,
                'summary'=>$market->ai_summary,'details'=>$market->analysis_details,
                'analyzed_at'=>optional($market->analyzed_at)->diffForHumans(),
            ],
        ]);
    }
}
