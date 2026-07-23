<?php

namespace App\Services;

use App\Models\Market;

class RiskCalculatorService
{
    /**
     * Calculate risk-based position size. This is an estimate: contract sizes
     * vary by broker, so users should confirm the result in their platform.
     */
    public function calculate(Market $market, float $balance, float $riskPct, float $entry, float $stop): array
    {
        $riskAmount = $balance * ($riskPct / 100);
        $distance = abs($entry - $stop);
        $pipSize = str_contains($market->symbol, 'JPY') ? 0.01 : ($market->category === 'forex' ? 0.0001 : 0.01);
        $stopPips = $distance / $pipSize;
        $pipValuePerLot = match ($market->category) {
            'forex' => 10.0,
            'metals' => 1.0,
            'crypto' => 1.0,
            'indices' => 1.0,
            default => 1.0,
        };
        $lots = $stopPips > 0 ? $riskAmount / ($stopPips * $pipValuePerLot) : 0;

        return [
            'risk_amount' => round($riskAmount, 2),
            'stop_distance' => round($distance, $market->precision()),
            'stop_pips' => round($stopPips, 1),
            'lot_size' => round(max(0, $lots), 3),
        ];
    }
}
