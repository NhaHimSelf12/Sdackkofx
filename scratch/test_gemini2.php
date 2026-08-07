<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$start = microtime(true);
$res = \Illuminate\Support\Facades\Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
    'contents' => [['parts' => [['text' => 'Hello']]]]
]);
echo '1.5-flash Time: ' . (microtime(true) - $start) . "\n";
echo "1.5-flash Status: " . $res->status() . "\n";

$start = microtime(true);
$res2 = \Illuminate\Support\Facades\Http::post('https://generativelanguage.googleapis.com/v1beta/models/gemini-3.5-flash:generateContent?key=' . env('GEMINI_API_KEY'), [
    'contents' => [['parts' => [['text' => 'Hello']]]]
]);
echo '3.5-flash Time: ' . (microtime(true) - $start) . "\n";
echo "3.5-flash Status: " . $res2->status() . "\n";
