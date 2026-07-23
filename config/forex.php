<?php

return [
    // AI analysis
    'openai_key' => env('OPENAI_API_KEY'),
    'openai_model' => env('OPENAI_MODEL', 'gpt-4o-mini'),

    // Market data provider
    'twelvedata_key' => env('TWELVEDATA_API_KEY'),

    // News provider
    'newsapi_key' => env('NEWSAPI_KEY'),

    // Default timeframe used by the signal engine & charts
    'default_timeframe' => 'H1',

    // Number of candles analysed per scan
    'candle_lookback' => 300,
];
