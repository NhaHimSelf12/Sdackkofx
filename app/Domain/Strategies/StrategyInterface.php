<?php

namespace App\Domain\Strategies;

interface StrategyInterface
{
    /** Short code, e.g. "SMC". */
    public function code(): string;

    public function name(): string;

    public function description(): string;

    /** @return string[] concepts the strategy is built on */
    public function concepts(): array;

    /**
     * Analyze candles and optionally return a signal.
     *
     * @param array<int, array{time:int, open:float, high:float, low:float, close:float}> $candles
     * @return array{direction:string, entry:float, stop_loss:float, take_profit:float, confidence:int, note:string}|null
     */
    public function analyze(array $candles): ?array;
}
