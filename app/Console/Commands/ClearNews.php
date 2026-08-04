<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearNews extends Command
{
    protected $signature = 'news:clear';
    protected $description = 'Clear all news items';

    public function handle()
    {
        DB::table('news_items')->truncate();
        $this->info('News cleared.');
    }
}
