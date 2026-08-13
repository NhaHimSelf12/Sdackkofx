<?php

use App\Http\Controllers\Api\ChartDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('markets/{market:symbol}')->group(function () {
    Route::get('/candles', [ChartDataController::class, 'candles'])->name('api.candles');
    Route::get('/trendlines', [ChartDataController::class, 'trendlines'])->name('api.trendlines');
    Route::get('/analysis', [ChartDataController::class, 'analysis'])->name('api.analysis');
});
Route::get('/cron/scan', function (\Illuminate\Http\Request $request) { if ($request->query('token') !== env('CRON_SECRET')) return response('Unauthorized', 401); \Illuminate\Support\Facades\Artisan::call('forex:scan', ['symbol' => 'XAUUSD']); return response()->json(['status' => 'success']); });
Route::get('/cron/track', function (\Illuminate\Http\Request $request) { if ($request->query('token') !== env('CRON_SECRET')) return response('Unauthorized', 401); \Illuminate\Support\Facades\Artisan::call('forex:track-signals'); return response()->json(['status' => 'success']); });
