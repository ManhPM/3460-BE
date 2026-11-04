<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait BugReporter
{
    public function sendBugReport(string $operation, array $context): void
    {
        $this->sendBugReportEmail($operation, $context);
    }

    public function sendBugReportTelegram(string $operation, array $context): void
    {
        $this->sendBugReportEmail($operation, $context);
    }

    public function reportThrowable(?\Throwable $e, string $operation = 'EXCEPTION', $request = null): void
    {
        if (!$e) {
            return;
        }

        if (
            $e instanceof \Symfony\Component\HttpKernel\Exception\HttpException ||
            $e instanceof \Illuminate\Validation\ValidationException ||
            $e instanceof \Illuminate\Auth\AuthenticationException ||
            $e instanceof \Illuminate\Auth\Access\AuthorizationException ||
            $e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException
        ) {
            return;
        }

        try {
            $req = $request ?: (app()->bound('request') ? request() : null);
            $data = [];
            if ($req && method_exists($req, 'except')) {
                $data = collect($req->except([
                    'password',
                    'password_confirmation',
                    'new_password',
                    'new_password_confirmation',
                    'old_password',
                    '_token',
                    '_method',
                ]))->map(function ($value) {
                    if (is_string($value) && preg_match('/^data:(image|video)\/[a-zA-Z]+;base64,/', $value)) {
                        return '[BASE64_DATA_REMOVED]';
                    }
                    if (is_string($value) && mb_strlen($value) > 500) {
                        return mb_substr($value, 0, 200) . '...[TRUNCATED]';
                    }
                    return $value;
                })->toArray();
            }

            $this->sendBugReportEmail($operation, [
                'route' => $req && method_exists($req, 'route') ? ($req->route()?->getName() ?? 'unknown') : 'cli',
                'url' => $req && method_exists($req, 'fullUrl') ? $req->fullUrl() : 'CLI',
                'method' => $req && method_exists($req, 'method') ? $req->method() : 'CLI',
                'data' => $data,
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ],
            ]);
        } catch (\Throwable $ex) {
            // Silent fail
        }
    }

    protected function normalizeBugPath(string $url): string
    {
        if ($url === '' || $url === 'N/A') {
            return 'N/A';
        }

        $parsedPath = parse_url($url, PHP_URL_PATH);
        $path = $parsedPath ?: $url;
        $path = preg_replace('#^https?://[^/]+#i', '', (string) $path);
        $path = explode('?', (string) $path)[0];
        $path = preg_replace('#/\d+(?=/|$)#', '/{id}', (string) $path);

        return $path ?: '/';
    }

    protected function normalizeBugFile(string $file): string
    {
        if ($file === '' || $file === 'N/A') {
            return '';
        }

        $basePath = rtrim(base_path(), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (str_starts_with($file, $basePath)) {
            return substr($file, strlen($basePath));
        }

        $normalized = preg_replace('#^/var/www/[^/]+/release_[^/]+/#', '', $file);

        return $normalized ?: $file;
    }

    protected function resolveProjectName(): string
    {
        $folder = basename(base_path());
        if ($folder === 'back-end') {
            $folder = basename(dirname(base_path())) . '/back-end';
        }
        $envName = config('app.name') ?: env('APP_NAME', '');
        if ($envName && !in_array(strtolower($envName), ['laravel', 'app'], true)) {
            return "{$folder} ({$envName})";
        }
        return $folder;
    }

    protected function acquireUniqueSheetBugLock(string $uniqueKey, bool $bypassCache = false): bool
    {
        if ($bypassCache) {
            return true;
        }

        $cacheKey = 'bug_sheet_unique_' . $uniqueKey;
        if (!Cache::add($cacheKey, true, 86400 * 30)) {
            return false;
        }

        try {
            $registryFile = storage_path('logs/bug_sheet_unique_keys.json');
            $keys = [];
            if (is_file($registryFile)) {
                $decoded = json_decode((string) @file_get_contents($registryFile), true);
                if (is_array($decoded)) {
                    $keys = $decoded;
                }
            }

            if (isset($keys[$uniqueKey])) {
                return false;
            }

            $keys[$uniqueKey] = now()->format('Y-m-d H:i:s');
            if (count($keys) > 5000) {
                $keys = array_slice($keys, -4000, null, true);
            }

            @file_put_contents($registryFile, json_encode($keys, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        } catch (\Throwable $e) {
            // Ignore file permission errors
        }

        return true;
    }

    /**
     * Gửi báo cáo bug qua Google Sheets (LOG BUG MEVIVU) & Telegram Bot.
     */
    public function sendBugReportEmail(string $operation, array $context): void
    {
        try {
            $method       = (string) ($context['method'] ?? (app()->bound('request') ? request()->method() : 'CLI'));
            $url          = (string) ($context['url'] ?? (app()->bound('request') ? request()->fullUrl() : 'N/A'));
            $pathOnly     = $this->normalizeBugPath($url);
            $route        = (string) ($context['route'] ?? (app()->bound('request') ? (request()->route()?->getName() ?? 'N/A') : 'N/A'));
            $errMsg       = trim((string) ($context['exception']['message'] ?? ($context['message'] ?? 'N/A')));
            $errFile      = (string) ($context['exception']['file'] ?? ($context['file'] ?? ''));
            $relativeFile = $this->normalizeBugFile($errFile);
            $errLine      = (string) ($context['exception']['line'] ?? ($context['line'] ?? ''));
            $fileLine     = $relativeFile !== '' ? "{$relativeFile}:{$errLine}" : 'N/A';
            $data         = json_encode($context['data'] ?? [], JSON_UNESCAPED_UNICODE);

            if (strlen($data) > 500) {
                $data = substr($data, 0, 500) . '... (truncated)';
            }

            $appName   = $this->resolveProjectName();
            $timestamp = now()->format('Y-m-d H:i:s');

            $copyContext = [
                'app'       => $appName,
                'operation' => $operation,
                'url'       => "{$method} {$url}",
                'path'      => $pathOnly,
                'route'     => $route,
                'error'     => $errMsg,
                'file'      => $fileLine,
                'data'      => $context['data'] ?? [],
                'timestamp' => $timestamp,
            ];

            $copyContextJsonStr = json_encode($copyContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            if (strlen($copyContextJsonStr) > 2000) {
                $copyContext['data'] = '[Truncated due to size]';
                $copyContextJsonStr = json_encode($copyContext, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            }

            // 1. Đẩy lỗi duy nhất (Unique theo Project + Path + Message + File:Line) lên Google Sheets LOG BUG MEVIVU
            $bypassCache     = (bool) ($context['bypass_cache'] ?? false);
            $uniqueSignature = "{$appName}|{$pathOnly}|{$errMsg}|{$fileLine}";
            $uniqueKey       = md5($uniqueSignature);

            if ($this->acquireUniqueSheetBugLock($uniqueKey, $bypassCache)) {
                $sheetWebhookUrl = env(
                    'GOOGLE_SHEET_BUG_WEBHOOK_URL',
                    'https://script.google.com/macros/s/AKfycbwpm0hQDbQ3POoJChFhnET-w7qYj2F4qy8ZzzYVOStm3dvIegRUwYdbEI93tOhpRCyRKg/exec'
                );

                if (!empty($sheetWebhookUrl)) {
                    try {
                        Http::timeout(8)
                            ->withOptions(['allow_redirects' => true])
                            ->post($sheetWebhookUrl, [
                                'spreadsheet_id' => '1ow0JE8kxuXNOH6yy3v5fIHl1U2dwRFtEplCzaVWTPoU',
                                'target'       => 'mevivu',
                                'unique_key'   => $uniqueKey,
                                'project'      => $appName,
                                'operation'    => $operation,
                                'method'       => $method,
                                'url'          => $url,
                                'path'         => $pathOnly,
                                'route'        => $route,
                                'message'      => $errMsg,
                                'file'         => $relativeFile,
                                'line'         => $errLine,
                                'file_line'    => $fileLine,
                                'data'         => $data,
                                'context_json' => $copyContextJsonStr,
                                'timestamp'    => $timestamp,
                            ]);
                    } catch (\Throwable $sheetEx) {
                        Log::warning('[BugReporter] Không thể đẩy log lên Google Sheets MEVIVU: ' . $sheetEx->getMessage());
                    }
                }
            }

            // 2. Gửi thông báo qua Telegram (tránh trùng lặp trong 5 phút)
            $telegramCacheKey = 'bug_report_' . $uniqueKey;
            if (!$bypassCache && !Cache::add($telegramCacheKey, true, 300)) {
                return;
            }

            $botToken = config('services.telegram_bug.bot_token') ?: (env('TELEGRAM_BUG_BOT_TOKEN') ?: env('TELEGRAM_BOT_TOKEN') ?: '8713520983:AAFSfxUScnSU4GCyu3aNiLQ0TkOpW9RLSpg');
            $chatId   = config('services.telegram_bug.chat_id') ?: (env('TELEGRAM_BUG_CHAT_ID') ?: env('TELEGRAM_CHAT_ID') ?: '8740034094');

            if (empty($botToken) || empty($chatId)) {
                return;
            }

            $safeCopyContextJson = htmlspecialchars($copyContextJsonStr, ENT_NOQUOTES, 'UTF-8');
            $safeUrl             = htmlspecialchars("{$method} {$url}", ENT_NOQUOTES, 'UTF-8');
            $safeRoute           = htmlspecialchars($route, ENT_NOQUOTES, 'UTF-8');
            $safeErrMsg          = htmlspecialchars($errMsg, ENT_NOQUOTES, 'UTF-8');
            $safeErrFile         = htmlspecialchars($relativeFile ?: $errFile, ENT_NOQUOTES, 'UTF-8');
            $safeData            = htmlspecialchars($data, ENT_NOQUOTES, 'UTF-8');

            $message  = "🚨 <b>BÁO CÁO LỖI HỆ THỐNG</b>\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "📌 <b>Ứng dụng:</b> {$appName}\n";
            $message .= "⚠️ <b>Thao tác:</b> {$operation}\n";
            $message .= "🌐 <b>URL:</b> <code>{$safeUrl}</code>\n";
            $message .= "🛣️ <b>Route:</b> <code>{$safeRoute}</code>\n\n";
            $message .= "❌ <b>Lỗi:</b> {$safeErrMsg}\n";
            if ($safeErrFile) {
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
                if ($safeErrFile) {
                    $message .= "📍 <b>Vị trí:</b> <code>{$safeErrFile}:{$errLine}</code>\n";
                }
                $message .= "\n📋 <b>Context (Chạm để copy):</b>\n";
                $message .= "<pre><code class=\"language-json\">{$truncatedJson}</code></pre>\n";
                $message .= "\n━━━━━━━━━━━━━━━━━━━━━━\n";
                $message .= "🕐 <b>Thời gian:</b> " . now()->format('H:i:s d/m/Y') . "\n";
                $message .= "🤖 Hệ thống tự động báo lỗi";
            }

            Http::timeout(8)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'                  => $chatId,
                'text'                     => $message,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi gửi bug report: ' . $e->getMessage());
        }
    }
}
