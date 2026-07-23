<?php

namespace App\Services;

use App\Models\NewsItem;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Fetches financial news (NewsAPI when configured) and scores each headline
 * with sentiment + impact using keyword heuristics. Seeded demo news is used
 * when no API key is configured.
 */
class NewsAnalysisService
{
    private const BULLISH = ['rally', 'surge', 'gains', 'beats', 'strong', 'record high', 'rate cut', 'dovish', 'stimulus', 'optimism', 'soars'];
    private const BEARISH = ['falls', 'drop', 'plunge', 'misses', 'weak', 'recession', 'rate hike', 'hawkish', 'fears', 'selloff', 'tumbles'];
    private const HIGH_IMPACT = ['fed', 'fomc', 'cpi', 'nfp', 'non-farm', 'interest rate', 'ecb', 'boe', 'boj', 'gdp', 'inflation'];

    private const SYMBOL_HINTS = [
        'XAUUSD' => ['gold', 'xau', 'bullion', 'precious metal'],
        'BTCUSD' => ['bitcoin', 'btc', 'crypto'],
        'ETHUSD' => ['ethereum', 'eth'],
        'EURUSD' => ['euro', 'ecb', 'eurozone'],
        'GBPUSD' => ['pound', 'sterling', 'boe', 'uk '],
        'USDJPY' => ['yen', 'boj', 'japan'],
        'US30' => ['dow', 'wall street', 'us stocks'],
    ];

    /** Pull latest headlines and store them as analyzed NewsItems. */
    public function refresh(): int
    {
        $key = config('forex.newsapi_key');
        if (! $key) {
            return 0; // demo news comes from the seeder
        }

        try {
            $articles = Http::timeout(15)->get('https://newsapi.org/v2/everything', [
                'q' => 'forex OR gold OR bitcoin OR "federal reserve" OR inflation',
                'language' => 'en',
                'sortBy' => 'publishedAt',
                'pageSize' => 30,
                'apiKey' => $key,
            ])->json('articles', []);
        } catch (\Throwable $e) {
            Log::warning('News fetch failed', ['error' => $e->getMessage()]);

            return 0;
        }

        $count = 0;
        foreach ($articles as $article) {
            $analysis = $this->analyzeHeadline($article['title'] ?? '');
            NewsItem::updateOrCreate(
                ['title' => $article['title']],
                [
                    'source' => $article['source']['name'] ?? null,
                    'url' => $article['url'] ?? null,
                    'published_at' => $article['publishedAt'] ?? now(),
                    'summary' => $article['description'] ?? null,
                    ...$analysis,
                ],
            );
            $count++;
        }

        return $count;
    }

    /**
     * @return array{sentiment:string, impact:string, symbols:array}
     */
    public function analyzeHeadline(string $title): array
    {
        $lower = mb_strtolower($title);

        $score = 0;
        foreach (self::BULLISH as $word) {
            $score += str_contains($lower, $word) ? 1 : 0;
        }
        foreach (self::BEARISH as $word) {
            $score -= str_contains($lower, $word) ? 1 : 0;
        }

        $impact = 'low';
        foreach (self::HIGH_IMPACT as $word) {
            if (str_contains($lower, $word)) {
                $impact = 'high';
                break;
            }
        }
        if ($impact === 'low' && abs($score) >= 1) {
            $impact = 'medium';
        }

        $symbols = [];
        foreach (self::SYMBOL_HINTS as $symbol => $hints) {
            foreach ($hints as $hint) {
                if (str_contains($lower, $hint)) {
                    $symbols[] = $symbol;
                    break;
                }
            }
        }

        return [
            'sentiment' => $score > 0 ? 'bullish' : ($score < 0 ? 'bearish' : 'neutral'),
            'impact' => $impact,
            'symbols' => $symbols,
        ];
    }
}
