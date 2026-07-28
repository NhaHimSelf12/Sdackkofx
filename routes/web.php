<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EaBotController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\TerminalController;
use App\Http\Controllers\MarketController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\RiskController;
use App\Http\Controllers\SignalController;
use App\Http\Controllers\SignalScanController;
use App\Http\Controllers\StrategyController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicController::class, 'index'])->name('home');
Route::get('/strategy/{publicStrategy}', [PublicController::class, 'showStrategy'])->name('public.strategy.show');
Route::get('/learn', [PublicController::class, 'learn'])->name('public.learn');

// Google Auth Routes
Route::get('auth/google', [\App\Http\Controllers\Auth\GoogleController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('auth/google/callback', [\App\Http\Controllers\Auth\GoogleController::class, 'handleGoogleCallback']);
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/app', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/terminal/{market:symbol}', [TerminalController::class, 'show'])->name('terminal.show');
    Route::get('/terminal/{market:symbol}/data', [TerminalController::class, 'data'])->middleware('throttle:60,1')->name('terminal.data');
    Route::get('/markets', [MarketController::class, 'index'])->name('markets.index');
    Route::get('/markets/{market:symbol}', [MarketController::class, 'show'])->name('markets.show');
    Route::post('/markets/{market:symbol}/watchlist', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');
    Route::get('/signals', [SignalController::class, 'index'])->name('signals.index');
    Route::post('/signals/refresh', SignalScanController::class)->middleware('throttle:3,1')->name('signals.refresh');
    Route::get('/news', [NewsController::class, 'index'])->name('news.index');
    Route::get('/strategies', [StrategyController::class, 'index'])->name('strategies.index');
    Route::get('/lessons', [\App\Http\Controllers\LessonController::class, 'index'])->name('lessons.index');

    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
    Route::get('/journal/create', [JournalController::class, 'create'])->name('journal.create');
    Route::post('/journal', [JournalController::class, 'store'])->name('journal.store');
    Route::patch('/journal/{journal}', [JournalController::class, 'update'])->name('journal.update');
    Route::delete('/journal/{journal}', [JournalController::class, 'destroy'])->name('journal.destroy');

    Route::get('/risk-calculator', [RiskController::class, 'index'])->name('risk.index');
    Route::post('/risk-calculator', [RiskController::class, 'calculate'])->name('risk.calculate');

    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::patch('/admin/users/{user}/role', [AdminController::class, 'userRole'])->name('admin.users.role');

    Route::get('/admin/ea-bots', [EaBotController::class, 'index'])->name('ea.index');
    Route::post('/admin/ea-bots', [EaBotController::class, 'store'])->name('ea.store');
    Route::patch('/admin/ea-bots/{bot}', [EaBotController::class, 'update'])->name('ea.update');
    Route::post('/admin/ea-bots/{bot}/run', [EaBotController::class, 'runNow'])->middleware('throttle:6,1')->name('ea.run');
    Route::delete('/admin/ea-bots/{bot}', [EaBotController::class, 'destroy'])->name('ea.destroy');

    Route::resource('admin/public-strategies', \App\Http\Controllers\PublicStrategyController::class)->names('admin.public-strategies');
});
