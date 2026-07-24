<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Set up /tmp directories for Vercel
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
];

foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }
}

// Copy SQLite DB to /tmp if it exists so it is writable (temporary only)
$dbSource = __DIR__.'/../database/database.sqlite';
$dbTemp = '/tmp/database.sqlite';
if (file_exists($dbSource) && !file_exists($dbTemp)) {
    copy($dbSource, $dbTemp);
}

$_ENV['DB_DATABASE'] = $dbTemp;
$_ENV['APP_STORAGE'] = '/tmp/storage';
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['APP_STORAGE'] = '/tmp/storage';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath('/tmp/storage');
    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>Vercel PHP Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
