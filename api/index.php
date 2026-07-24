<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Set up /tmp directories for Vercel
$directories = [
    '/tmp/storage/framework/views',
    '/tmp/storage/framework/cache/data',
    '/tmp/storage/logs',
    '/tmp/bootstrap/cache',
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
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// Override bootstrap cache paths for Vercel
$_ENV['APP_SERVICES_CACHE'] = '/tmp/bootstrap/cache/services.php';
$_ENV['APP_PACKAGES_CACHE'] = '/tmp/bootstrap/cache/packages.php';
$_ENV['APP_CONFIG_CACHE'] = '/tmp/bootstrap/cache/config.php';
$_ENV['APP_ROUTES_CACHE'] = '/tmp/bootstrap/cache/routes.php';
$_ENV['APP_EVENTS_CACHE'] = '/tmp/bootstrap/cache/events.php';

require __DIR__.'/../vendor/autoload.php';

try {
    $app = require_once __DIR__.'/../bootstrap/app.php';
    $app->useStoragePath('/tmp/storage');
    
    // Inject a custom exception handler to see the ORIGINAL error
    $app->instance(
        Illuminate\Contracts\Debug\ExceptionHandler::class,
        new class($app) extends Illuminate\Foundation\Exceptions\Handler {
            public function render($request, \Throwable $e) {
                echo "<h1>ORIGINAL ERROR FOUND:</h1>";
                echo "<pre>" . $e->getMessage() . "</pre>";
                echo "<pre>" . $e->getTraceAsString() . "</pre>";
                exit;
            }
        }
    );

    $app->handleRequest(Request::capture());
} catch (\Throwable $e) {
    echo "<h1>Vercel PHP Error</h1>";
    echo "<pre>" . $e->getMessage() . "</pre>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
