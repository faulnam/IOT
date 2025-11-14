<?php

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

if (!function_exists('sendTelegram')) {
    function sendTelegram($text)
    {
        $token  = env('TELEGRAM_BOT_TOKEN');
        $chatId = env('TELEGRAM_CHAT_ID');

        // Proper behaviour: verify SSL using system CA bundle.
        // If system CA is misconfigured in your environment (common on some Windows setups),
        // the request may fail with cURL error 77. To quickly test, we allow a temporary
        // fallback that disables SSL verification. DO NOT keep this in production.
        try {
            // Try normal request first (secure)
            $res = Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $text,
                'parse_mode' => 'Markdown'
            ]);

            return $res;
        } catch (\Throwable $e) {
            Log::warning('sendTelegram secure request failed, retrying with verify=false', ['error' => $e->getMessage()]);

            try {
                // Fallback for testing/dev only: disable SSL verification
                $res = Http::withOptions(['verify' => false])->post("https://api.telegram.org/bot{$token}/sendMessage", [
                    'chat_id' => $chatId,
                    'text' => $text,
                    'parse_mode' => 'Markdown'
                ]);

                return $res;
            } catch (\Throwable $e2) {
                Log::error('sendTelegram failed (verify=false fallback also failed)', ['error' => $e2->getMessage()]);
                return null;
            }
        }
    }
}
