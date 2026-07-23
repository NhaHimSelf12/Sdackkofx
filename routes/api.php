<?php

use App\Http\Controllers\Api\ChartDataController;
use Illuminate\Support\Facades\Route;

Route::prefix('markets/{market:symbol}')->group(function () {
    Route::get('/candles', [ChartDataController::class, 'candles'])->name('api.candles');
    Route::get('/trendlines', [ChartDataController::class, 'trendlines'])->name('api.trendlines');
    Route::get('/analysis', [ChartDataController::class, 'analysis'])->name('api.analysis');
});
