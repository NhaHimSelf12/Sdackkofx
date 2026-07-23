<?php

namespace App\Domain\Strategies;

class StrategyRegistry
{
    /**
     * All available strategies. Add new strategies here.
     *
     * @return StrategyInterface[]
     */
    public static function all(): array
    {
        return [
            new SmcStrategy(),
            new IctStrategy(),
            new MsnrStrategy(),
            new TechnicalConfluenceStrategy(),
        ];
    }

    public static function find(string $code): ?StrategyInterface
    {
        foreach (self::all() as $strategy) {
            if ($strategy->code() === strtoupper($code)) {
                return $strategy;
            }
        }

        return null;
    }
}
