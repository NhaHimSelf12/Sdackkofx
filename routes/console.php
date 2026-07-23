<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Keep signals synchronized every five minutes.
// Production cron: * * * * * php /path/to/artisan schedule:run
// Local development: php artisan schedule:work
Schedule::command('forex:scan')->everyFiveMinutes()->withoutOverlapping();

// Let EA bots settle open paper trades and take new entries every minute.
Schedule::command('forex:ea-run')->everyMinute()->withoutOverlapping();
