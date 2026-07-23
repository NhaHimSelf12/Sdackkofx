<?php

namespace App\Console\Commands;

use App\Models\EaBot;
use App\Services\EaBotEngine;
use Illuminate\Console\Command;

class RunEaBots extends Command
{
    protected $signature = 'forex:ea-run {bot? : Bot id or name (defaults to all running bots)}';

    protected $description = 'Run EA bots: settle open paper trades against the feed and take new entries';

    public function handle(EaBotEngine $engine): int
    {
        $arg = $this->argument('bot');
        $bots = $arg
            ? EaBot::where('id', $arg)->orWhere('name', $arg)->get()
            : EaBot::where('status', 'running')->get();

        if ($bots->isEmpty()) {
            $this->warn('No matching EA bots. Create one under Admin > EA Bots.');
            return self::SUCCESS;
        }

        foreach ($bots as $bot) {
            $result = $engine->run($bot);
            $this->info(sprintf('%s [%s] opened=%d closed=%d equity=$%s today=%d',
                $bot->name, $bot->mode, $result['opened'], $result['closed'], number_format($bot->equity(), 2), $bot->positions_today));
        }

        return self::SUCCESS;
    }
}
