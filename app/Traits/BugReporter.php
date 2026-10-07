<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait BugReporter
{
    /**
     * Gửi báo cáo bug qua Telegram Bot.
     * Cấu hình trong .env: TELEGRAM_BUG_BOT_TOKEN, TELEGRAM_BUG_CHAT_ID
     * Hoặc tự động dùng key mặc định gắn trực tiếp
     */
    public function sendBugReportEmail(string $operation, array $context): void
    {
        // Cho phép gửi báo cáo lỗi kể cả khi APP_DEBUG=true (trừ khi TELEGRAM_BUG_REPORT_IN_DEBUG=false)
        if (env('APP_DEBUG', false) && !env('TELEGRAM_BUG_REPORT_IN_DEBUG', true)) {
            return;
        }

        try {
            $method    = $context['method'] ?? 'N/A';
            $url       = $context['url'] ?? 'N/A';
            $route     = $context['route'] ?? 'N/A';
            $errMsg    = $context['exception']['message'] ?? 'N/A';
            $errFile   = $context['exception']['file'] ?? '';
            $errLine   = $context['exception']['line'] ?? '';
            $data      = json_encode($context['data'] ?? [], JSON_UNESCAPED_UNICODE);

            // Giới hạn data để không quá dài
            if (strlen($data) > 500) {
                $data = substr($data, 0, 500) . '... (truncated)';
            }

            $bypassCache = $context['bypass_cache'] ?? false;
            $plainBody = "{$operation}|{$method} {$url}|{$errMsg}|{$errFile}:{$errLine}";
            $cacheKey  = 'bug_report_' . md5($plainBody);

            // Tránh gửi trùng lặp trong 5 phút (nếu không bật bypass_cache)
            if (!$bypassCache && !Cache::add($cacheKey, true, 300)) {
                return;
            }

            $botToken = env('TELEGRAM_BUG_BOT_TOKEN') ?: '8713520983:AAFSfxUScnSU4GCyu3aNiLQ0TkOpW9RLSpg';
            $chatId   = env('TELEGRAM_BUG_CHAT_ID') ?: '8740034094';

            if (empty($botToken) || empty($chatId)) {
                Log::warning('[BugReporter] Chưa cấu hình TELEGRAM_BUG_BOT_TOKEN hoặc TELEGRAM_BUG_CHAT_ID');
                return;
            }

            $appName = env('APP_NAME', 'App');

            // Format Context object để copy nhanh bằng 1-tap trong Telegram
            $copyContext = [
                'app'       => $appName,
                'operation' => $operation,
                'url'       => "{$method} {$url}",
                'route'     => $route,
                'error'     => $errMsg,
                'file'      => $errFile ? "{$errFile}:{$errLine}" : 'N/A',
                'data'      => $context['data'] ?? [],
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ];

            $copyContextJsonStr = json_encode($copyContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if (strlen($copyContextJsonStr) > 2000) {
                $copyContext['data'] = '[Truncated due to size]';
                $copyContextJsonStr = json_encode($copyContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            $safeCopyContextJson = htmlspecialchars($copyContextJsonStr, ENT_NOQUOTES, 'UTF-8');
            $safeUrl             = htmlspecialchars("{$method} {$url}", ENT_NOQUOTES, 'UTF-8');
            $safeRoute           = htmlspecialchars($route, ENT_NOQUOTES, 'UTF-8');
            $safeErrMsg          = htmlspecialchars($errMsg, ENT_NOQUOTES, 'UTF-8');
            $safeErrFile         = htmlspecialchars($errFile, ENT_NOQUOTES, 'UTF-8');
            $safeData            = htmlspecialchars($data, ENT_NOQUOTES, 'UTF-8');

            $message  = "🚨 <b>BÁO CÁO LỖI HỆ THỐNG</b>\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "📌 <b>Ứng dụng:</b> {$appName}\n";
            $message .= "⚠️ <b>Thao tác:</b> {$operation}\n";
            $message .= "🌐 <b>URL:</b> <code>{$safeUrl}</code>\n";
            $message .= "🛣️ <b>Route:</b> <code>{$safeRoute}</code>\n\n";

            $message .= "❌ <b>Lỗi:</b> {$safeErrMsg}\n";
            if ($errFile) {
                $message .= "📍 <b>Vị trí:</b> <code>{$safeErrFile}:{$errLine}</code>\n";
            }

            if ($data && $data !== '[]' && $data !== '{}') {
                $message .= "\n📋 <b>Dữ liệu:</b>\n<pre>{$safeData}</pre>\n";
            }

            $message .= "\n📋 <b>Context (Chạm để copy):</b>\n";
            $message .= "<pre><code class=\"language-json\">{$safeCopyContextJson}</code></pre>\n";

            $message .= "\n━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🕐 <b>Thời gian:</b> " . now()->format('H:i:s d/m/Y') . "\n";
            $message .= "🤖 Hệ thống tự động báo lỗi";

            if (mb_strlen($message) > 3900) {
                $truncatedJson = htmlspecialchars(substr($copyContextJsonStr, 0, 1200) . "\n... (truncated)", ENT_NOQUOTES, 'UTF-8');
                $message  = "🚨 <b>BÁO CÁO LỖI HỆ THỐNG</b>\n";
                $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
                $message .= "📌 <b>Ứng dụng:</b> {$appName}\n";
                $message .= "⚠️ <b>Thao tác:</b> {$operation}\n";
                $message .= "🌐 <b>URL:</b> <code>{$safeUrl}</code>\n";
                $message .= "🛣️ <b>Route:</b> <code>{$safeRoute}</code>\n\n";
                $message .= "❌ <b>Lỗi:</b> " . substr($safeErrMsg, 0, 500) . "\n";
                if ($errFile) {
                    $message .= "📍 <b>Vị trí:</b> <code>{$safeErrFile}:{$errLine}</code>\n";
                }
                $message .= "\n📋 <b>Context (Chạm để copy):</b>\n";
                $message .= "<pre><code class=\"language-json\">{$truncatedJson}</code></pre>\n";
                $message .= "\n━━━━━━━━━━━━━━━━━━━━━━\n";
                $message .= "🕐 <b>Thời gian:</b> " . now()->format('H:i:s d/m/Y') . "\n";
                $message .= "🤖 Hệ thống tự động báo lỗi";
            }

            $response = Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'                  => $chatId,
                'text'                     => $message,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
            ]);

            if (!$response->successful()) {
                Log::error('❌ Lỗi gửi bug report qua Telegram (Telegram API Error): ' . $response->body(), [
                    'status'  => $response->status(),
                    'chat_id' => $chatId,
                ]);
            }
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi gửi bug report qua Telegram: ' . $e->getMessage());
        }
    }
}
