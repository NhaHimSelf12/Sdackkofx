<?php

namespace Database\Seeders;

use App\Models\NewsItem;
use App\Services\NewsAnalysisService;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $analyzer = app(NewsAnalysisService::class);

        $headlines = [
            ['title' => 'Gold surges to record high as Fed signals rate cut ahead of September meeting', 'source' => 'Reuters', 'hours' => 2, 'summary' => 'Bullion extended gains as markets priced in easier policy; a weaker dollar and falling real yields supported the move.'],
            ['title' => 'Bitcoin rally stalls near $65,000 as ETF inflows cool', 'source' => 'CoinDesk', 'hours' => 4, 'summary' => 'BTC consolidates below key resistance while spot ETF demand slows for a second week.'],
            ['title' => 'US CPI comes in hotter than expected, dollar gains broadly', 'source' => 'Bloomberg', 'hours' => 7, 'summary' => 'Sticky inflation pushed back on rate-cut bets, lifting the dollar index against majors.'],
            ['title' => 'ECB officials hint at pause after back-to-back cuts, euro steadies', 'source' => 'FT', 'hours' => 10, 'summary' => 'Policymakers signalled a data-dependent stance; EURUSD held above support.'],
            ['title' => 'Pound falls as UK GDP misses forecasts for second quarter', 'source' => 'Reuters', 'hours' => 14, 'summary' => 'Weak growth data raised expectations of earlier BoE easing, pressuring sterling.'],
            ['title' => 'Yen tumbles as BoJ keeps ultra-loose policy, intervention fears grow', 'source' => 'Nikkei', 'hours' => 18, 'summary' => 'USDJPY pushed toward multi-decade highs, keeping traders alert for MoF action.'],
            ['title' => 'Ethereum gains after major exchange expands staking products', 'source' => 'CoinDesk', 'hours' => 22, 'summary' => 'ETH outperformed BTC on renewed institutional staking demand.'],
            ['title' => 'Dow closes at record high on strong earnings from industrials', 'source' => 'CNBC', 'hours' => 26, 'summary' => 'US30 extended its uptrend as earnings season beats broadened.'],
            ['title' => 'Oil selloff deepens on demand fears, commodity currencies weaken', 'source' => 'Bloomberg', 'hours' => 30, 'summary' => 'CAD and AUD slipped alongside crude as global demand outlook dimmed.'],
            ['title' => 'Non-farm payrolls preview: markets brace for volatility across FX majors', 'source' => 'ForexLive', 'hours' => 34, 'summary' => 'NFP Friday could set the tone for the dollar into the FOMC blackout period.'],
        ];

        foreach ($headlines as $item) {
            $analysis = $analyzer->analyzeHeadline($item['title']);
            NewsItem::updateOrCreate(
                ['title' => $item['title']],
                [
                    'source' => $item['source'],
                    'published_at' => now()->subHours($item['hours']),
                    'summary' => $item['summary'],
                    ...$analysis,
                ],
            );
        }
    }
}
