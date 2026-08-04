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
        try {
            // Fetch from free CNBC Finance RSS feed
            $response = Http::timeout(15)->get('https://search.cnbc.com/rs/search/combinedcms/view.xml?partnerId=wrss01&id=10000664');
            if (!$response->successful()) {
                return 0;
            }
            
            $xml = simplexml_load_string($response->body());
            if (!$xml || !isset($xml->channel->item)) {
                return 0;
            }
            
            $articles = [];
            foreach ($xml->channel->item as $item) {
                // Shorten description to max 200 characters to keep UI clean
                $description = strip_tags((string)$item->description);
                if (strlen($description) > 200) {
                    $description = substr($description, 0, 197) . '...';
                }
                
                $articles[] = [
                    'title' => (string)$item->title,
                    'url' => (string)$item->link,
                    'publishedAt' => date('Y-m-d H:i:s', strtotime((string)$item->pubDate)),
                    'description' => $description,
                    'source' => 'CNBC',
                ];
                if (count($articles) >= 15) break; // Limit to 15 recent items
            }
        } catch (\Throwable $e) {
            Log::warning('News fetch failed', ['error' => $e->getMessage()]);
            return 0;
        }

        $tr = new \Stichoza\GoogleTranslate\GoogleTranslate('km', 'en');
        $count = 0;
        
        foreach ($articles as $article) {
            if (empty($article['url'])) continue;
            
            // Analyze sentiment using the ORIGINAL English text
            $analysis = $this->analyzeHeadline($article['title'] ?? '');
            
            // Try to translate to Khmer
            try {
                // Add a small delay to prevent Google Translate API rate limiting (429 Too Many Requests)
                usleep(500000); // 0.5 seconds
                $kmTitle = $tr->translate($article['title'] ?? '');
                
                usleep(500000); // 0.5 seconds
                $kmSummary = $tr->translate($article['description'] ?? '');
            } catch (\Throwable $e) {
                Log::error('Translation error: ' . $e->getMessage());
                $kmTitle = '';
                $kmSummary = '';
            }

            NewsItem::updateOrCreate(
                ['url' => $article['url']], // Unique key is URL
                [
                    'title' => $article['title'],
                    'title_km' => $kmTitle,
                    'source' => $article['source'] ?? null,
                    'published_at' => $article['publishedAt'] ?? now(),
                    'summary' => $article['description'],
                    'summary_km' => $kmSummary,
                    'sentiment' => $analysis['sentiment'],
                    'impact' => $analysis['impact'],
                    'symbols' => $analysis['symbols'],
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
