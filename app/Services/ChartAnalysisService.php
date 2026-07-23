<?php

namespace App\Services;

/** On-the-fly overlays for the trading terminal. */
class ChartAnalysisService
{
    public function analyze(array $candles): array
    {
        return [
            'fvg' => $this->fairValueGaps($candles),
            'trendlines' => $this->trendlines($candles),
            'volume_profile' => $this->volumeProfile($candles),
        ];
    }

    private function fairValueGaps(array $candles): array
    {
        $zones = []; $n = count($candles);
        for ($i = 2; $i < $n; $i++) {
            $a = $candles[$i - 2]; $c = $candles[$i];
            if ($a['high'] < $c['low']) {
                $zones[] = ['type'=>'bullish','start_time'=>$a['time'],'end_time'=>$c['time'],'bottom'=>$a['high'],'top'=>$c['low'],'mid'=>($a['high']+$c['low'])/2];
            } elseif ($a['low'] > $c['high']) {
                $zones[] = ['type'=>'bearish','start_time'=>$a['time'],'end_time'=>$c['time'],'bottom'=>$c['high'],'top'=>$a['low'],'mid'=>($a['low']+$c['high'])/2];
            }
        }
        return array_slice($zones, -12);
    }

    private function trendlines(array $candles): array
    {
        $window = 4; $highs=[]; $lows=[]; $n=count($candles);
        for($i=$window;$i<$n-$window;$i++){
            $isH=$isL=true;
            for($j=$i-$window;$j<=$i+$window;$j++){
                if($candles[$j]['high']>$candles[$i]['high'])$isH=false;
                if($candles[$j]['low']<$candles[$i]['low'])$isL=false;
            }
            if($isH)$highs[]=['time'=>$candles[$i]['time'],'price'=>$candles[$i]['high']];
            if($isL)$lows[]=['time'=>$candles[$i]['time'],'price'=>$candles[$i]['low']];
        }
        $out=[];
        if(count($highs)>=2){$a=$highs[count($highs)-2];$b=$highs[count($highs)-1];$out[]=['side'=>'sell','start'=>$a,'end'=>$b];}
        if(count($lows)>=2){$a=$lows[count($lows)-2];$b=$lows[count($lows)-1];$out[]=['side'=>'buy','start'=>$a,'end'=>$b];}
        return $out;
    }

    private function volumeProfile(array $candles, int $bins = 18): array
    {
        if (!$candles) return [];
        $low=min(array_column($candles,'low')); $high=max(array_column($candles,'high'));
        $step=($high-$low)/$bins; if($step<=0)return [];
        $profile=array_fill(0,$bins,0.0);
        foreach($candles as $c){$mid=($c['high']+$c['low']+$c['close'])/3;$idx=min($bins-1,max(0,(int)(($mid-$low)/$step)));$profile[$idx]+=(float)($c['volume']??1);}
        $max=max($profile)?:1; $out=[];
        foreach($profile as $i=>$volume)$out[]=['price'=>round($low+($i+.5)*$step,5),'volume'=>round($volume,2),'percent'=>round($volume/$max*100,1)];
        return array_reverse($out);
    }
}
