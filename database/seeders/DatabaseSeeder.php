<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@fxcommand.test'], [
            'name' => 'FX Admin', 'password' => Hash::make('password'), 'role' => 'admin',
            'account_balance' => 10000, 'default_risk_pct' => 1,
        ]);

        $this->call([
            MarketSeeder::class,
            StrategySeeder::class,
            NewsSeeder::class,
        ]);
    }
}
