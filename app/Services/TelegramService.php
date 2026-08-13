<?php

namespace App\Services;

use App\Models\Signal;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    protected string $token;
    protected string $chatId;

    public function __construct()
    {
        $this->token  = config('services.telegram.bot_token', '');
        $this->chatId = config('services.telegram.chat_id', '');
    }

    /**
     * Send all given signals to the Telegram channel with a beautiful UI.
     *
     * @param  \Illuminate\Support\Collection|Signal[]  $signals
     */
    public function sendSignals($signals): bool
    {
        if (empty($this->token) || empty($this->chatId) || count($signals) === 0) {
            return false;
        }

        foreach ($signals as $signal) {
            $message = $this->formatSignal($signal);
            $this->send($message);
            usleep(300_000); // 300ms delay between messages to avoid rate limit
        }

        return true;
    }

    /**
     * Format a single signal into a beautiful Telegram message.
     */
    protected function formatSignal(Signal $signal): string
    {
        $market   = $signal->market;
        $symbol   = $market->symbol ?? 'N/A';
        $name     = $market->name ?? '';
        $isBuy    = strtolower($signal->direction) === 'buy';
        $arrow    = $isBuy ? '🟢' : '🔴';
        $dirLabel = strtoupper($signal->direction);
        $strategy = strtoupper($signal->strategy ?? 'TECHNICAL');

        // Price formatting
        $precision = $market ? $market->precision() : 2;
        $entry = number_format($signal->entry, $precision);
        $sl    = number_format($signal->stop_loss, $precision);
        $tp    = number_format($signal->take_profit, $precision);

        // Calculate pips/points for SL & TP distance
        $slDistance = abs($signal->entry - $signal->stop_loss);
        $tpDistance = abs($signal->take_profit - $signal->entry);
        $slPips = number_format($slDistance, $precision);
        $tpPips = number_format($tpDistance, $precision);

        // Risk:Reward
        $rr = $signal->risk_reward ? number_format($signal->risk_reward, 1) : 'N/A';

        // Confidence
        $conf = $signal->confidence ? $signal->confidence . '%' : 'N/A';

        // Confidence bar visual
        $confValue = intval($signal->confidence ?? 0);
        $confBar = $this->buildConfidenceBar($confValue);

        // Risk level based on RR
        $rrValue = floatval($signal->risk_reward ?? 0);
        $riskEmoji = $rrValue >= 3 ? '🟢 Low Risk' : ($rrValue >= 2 ? '🟡 Moderate' : '🔴 High Risk');

        // Timeframe
        $tf = strtoupper($signal->timeframe ?? 'H1');

        // Build the message
        $msg  = "━━━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "{$arrow} *{$dirLabel} {$symbol}* · {$strategy}\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";

        $msg .= "📊 *{$symbol}* — _{$name}_\n";
        $msg .= "⏱ Timeframe: `{$tf}`\n\n";

        $msg .= "▫️ *Entry Price:*     `{$entry}`\n";
        $msg .= "🛑 *Stop Loss:*       `{$sl}`  _({$slPips} pts)_\n";
        $msg .= "🎯 *Take Profit:*     `{$tp}`  _({$tpPips} pts)_\n\n";

        if ($signal->tp1) {
            $msg .= "🎯 *TP1:*  `" . number_format($signal->tp1, $precision) . "`\n";
        }
        if ($signal->tp2) {
            $msg .= "🎯 *TP2:*  `" . number_format($signal->tp2, $precision) . "`\n";
        }
        if ($signal->tp1 || $signal->tp2) {
            $msg .= "\n";
        }

        if ($signal->note) {
            $msg .= "📝 _{$signal->note}_\n\n";
        }

        $msg .= "⏰ " . now()->format('d M Y · H:i') . " UTC\n";
        $msg .= "🤖 _Powered by Sdach KOFX AI_\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━━";

        return $msg;
    }

    public function sendStatusAlert(Signal $signal, string $statusType): void
    {
        $symbol = $signal->market->symbol ?? 'N/A';
        $isBuy  = strtolower($signal->direction) === 'buy';
        $arrow  = $isBuy ? '🟢' : '🔴';
        
        $msg = "🔔 *{$symbol} Update*\n━━━━━━━━━━━━━━━━━━━━━━\n";

        switch ($statusType) {
            case 'active':
                $msg .= "✅ *Signal is Active*\nPrice has reached the entry zone.";
                break;
            case 'tp1':
                $msg .= "🎉 *Entry is hit tp1*\nFirst target achieved! Consider moving SL to entry.";
                break;
            case 'tp2':
                $msg .= "🎉 *Hit tp2*\nSecond target achieved! Securing more profits.";
                break;
            case 'tp':
                $msg .= "🚀 *Full TP Hit*\nAll targets smashed! Great trade.";
                break;
            case 'sl':
                $msg .= "❌ *Signal hit SL*\nStop loss hit. We will catch the next one.";
                break;
        }

        $msg .= "\n━━━━━━━━━━━━━━━━━━━━━━";

        $this->send($msg);
    }

    /**
     * Send a message via Telegram Bot API.
     */
    protected function send(string $text): void
    {
        try {
            $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

            Http::post($url, [
                'chat_id'    => $this->chatId,
                'text'       => $text,
                'parse_mode' => 'Markdown',
                'disable_web_page_preview' => true,
            ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram send failed: ' . $e->getMessage());
        }
    }
}
