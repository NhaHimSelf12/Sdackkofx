<?php

namespace Database\Seeders;

use App\Domain\Strategies\StrategyRegistry;
use App\Models\Strategy;
use Illuminate\Database\Seeder;

class StrategySeeder extends Seeder
{
    public function run(): void
    {
        foreach (StrategyRegistry::all() as $strategy) {
            Strategy::updateOrCreate(
                ['code' => $strategy->code()],
                [
                    'name' => $strategy->name(),
                    'description' => $strategy->description(),
                    'concepts' => $strategy->concepts(),
                    'enabled' => true,
                ],
            );
        }
    }
}
