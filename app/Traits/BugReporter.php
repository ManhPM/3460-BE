<?php

namespace App\Traits;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

trait BugReporter
{
    /**
     * Alias tương thích với các dự án gọi sendBugReport / sendBugReportTelegram
     */
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

    /**
     * Chuẩn hóa URL thành Path tĩnh (bỏ domain, query string và thay ID số bằng {id})
     * để chống trùng lặp cùng 1 lỗi trên cùng 1 endpoint.
     */
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

    /**
     * Chuẩn hóa đường dẫn file lỗi (bỏ base_path để đồng nhất giữa các bản release).
     */
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
        $normalized = preg_replace('#^/var/www/[^/]+/#', '', (string) $normalized);

        return $normalized ?: $file;
    }

    /**
     * Tạo chuỗi Prompt 1 dòng (không chứa xuống dòng) để click 1 ô trên Google Sheet hoặc Telegram
     * là copy dán thẳng vào Antigravity/Cursor để AI tự mở đúng file fix ngay.
     */
    protected function buildQuickFixPrompt(
        string $projectFolder,
        string $appName,
        string $operation,
        string $method,
        string $url,
        string $route,
        string $errMsg,
        string $relativeFile,
        string $errLine,
        string $data
    ): string {
        $localRoot = "/Applications/XAMPP/xamppfiles/htdocs/{$projectFolder}";
        $fileMention = $relativeFile !== ''
            ? "@[{$localRoot}/{$relativeFile}]" . ($errLine !== '' ? " (dòng {$errLine})" : '')
            : "@[{$localRoot}]";

        $cleanMsg  = preg_replace('/\s+/', ' ', trim($errMsg));
        $cleanData = preg_replace('/\s+/', ' ', trim($data));

        return "Hãy kiểm tra và fix triệt để lỗi sau trong dự án @[{$localRoot}] ({$appName}) — "
            . "File lỗi: {$fileMention} — "
            . "Message lỗi: {$cleanMsg} — "
            . "Endpoint: {$method} {$url} (Route: {$route} | Thao tác: {$operation}) — "
            . "Payload: {$cleanData}";
    }

    /**
     * Kiểm tra và đánh dấu lỗi duy nhất (Unique theo Project + Path + Message + File:Line)
     * để không bao giờ đẩy trùng lặp lên Google Sheets.
     */
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
            // Bỏ qua lỗi ghi file registry nếu phân quyền hạn chế
        }

        return true;
    }

    /**
     * Gửi báo cáo bug hoặc thông báo hệ thống qua Google Sheets (LOG BUG MEVIVU) & Telegram Bot.
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

            $folder = basename(base_path()); if ($folder === 'back-end') { $folder = basename(dirname(base_path())) . '/back-end'; } $envName = config('app.name') ?: env('APP_NAME', ''); $appName = ($envName && !in_array(strtolower($envName), ['laravel', 'app'], true)) ? "{$folder} ({$envName})" : $folder;

            // Phân loại loại thông báo (error, warning, success, info)
            $type = $context['type'] ?? null;
            if (!$type) {
                if (str_contains(strtoupper($operation), 'VERIFICATION') || str_contains(strtoupper($operation), 'SUCCESS')) {
                    $type = 'success';
                } elseif (str_contains(strtoupper($operation), 'WARN')) {
                    $type = 'warning';
                } else {
                    $type = 'error';
                }
            }

            $timestamp = now()->format('Y-m-d H:i:s');

            $quickFixPrompt = $this->buildQuickFixPrompt(
                $folder,
                $appName,
                $operation,
                $method,
                $url,
                $route,
                $errMsg,
                $relativeFile,
                $errLine,
                $data
            );

            $copyContext = [
                'app'       => $appName,
                'type'      => $type,
                'operation' => $operation,
                'url'       => "{$method} {$url}",
                'path'      => $pathOnly,
                'route'     => $route,
                'message'   => $errMsg,
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

            if ($type === 'error' && $this->acquireUniqueSheetBugLock($uniqueKey, $bypassCache)) {
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
                                'target'         => 'mevivu',
                                'unique_key'     => $uniqueKey,
                                'project'        => $appName,
                                'operation'      => $operation,
                                'type'           => $type,
                                'method'         => $method,
                                'url'            => $url,
                                'path'           => $pathOnly,
                                'route'          => $route,
                                'message'        => $errMsg,
                                'file'           => $relativeFile,
                                'line'           => $errLine,
                                'file_line'      => $fileLine,
                                'data'           => $data,
                                'copy_prompt'    => $quickFixPrompt,
                                'context_json'   => $quickFixPrompt,
                                'timestamp'      => $timestamp,
                            ]);
                    } catch (\Throwable $sheetEx) {
                        Log::warning('[BugReporter] Không thể đẩy log lên Google Sheets GUD: ' . $sheetEx->getMessage());
                    }
                }
            }

            // 2. Gửi thông báo qua Telegram (tránh trùng lặp trong 3 phút)
            $telegramCacheKey = 'bug_report_' . $uniqueKey;
            if (!$bypassCache && !Cache::add($telegramCacheKey, true, 180)) {
                return;
            }

            $botToken = config('custom_api.telegram_bug_bot_token') ?: (env('TELEGRAM_BUG_BOT_TOKEN') ?: config('custom_api.telegram_bot_token') ?: env('TELEGRAM_BOT_TOKEN'));
            $chatId   = config('custom_api.telegram_bug_chat_id') ?: (env('TELEGRAM_BUG_CHAT_ID') ?: config('custom_api.telegram_chat_id') ?: env('TELEGRAM_CHAT_ID'));

            if (empty($botToken) || empty($chatId)) {
                return;
            }

            switch ($type) {
                case 'success':
                    $headerTitle = "✅ <b>THÔNG BÁO HỆ THỐNG</b>";
                    $labelContent = "📌 <b>Nội dung:</b>";
                    $footerText = "🤖 Hệ thống thông báo tự động";
                    break;
                case 'warning':
                    $headerTitle = "⚠️ <b>CẢNH BÁO HỆ THỐNG</b>";
                    $labelContent = "⚠️ <b>Cảnh báo:</b>";
                    $footerText = "🤖 Hệ thống cảnh báo tự động";
                    break;
                case 'info':
                    $headerTitle = "ℹ️ <b>THÔNG TIN HỆ THỐNG</b>";
                    $labelContent = "ℹ️ <b>Thông tin:</b>";
                    $footerText = "🤖 Hệ thống thông tin tự động";
                    break;
                default:
                    $headerTitle = "🚨 <b>BÁO CÁO LỖI HỆ THỐNG</b>";
                    $labelContent = "❌ <b>Chi tiết lỗi:</b>";
                    $footerText = "🤖 Hệ thống tự động báo lỗi";
                    break;
            }

            $safeQuickFixPrompt  = htmlspecialchars($quickFixPrompt, ENT_NOQUOTES, 'UTF-8');
            $safeCopyContextJson = htmlspecialchars($copyContextJsonStr, ENT_NOQUOTES, 'UTF-8');
            $safeUrl             = htmlspecialchars("{$method} {$url}", ENT_NOQUOTES, 'UTF-8');
            $safeRoute           = htmlspecialchars($route, ENT_NOQUOTES, 'UTF-8');
            $safeErrMsg          = htmlspecialchars($errMsg, ENT_NOQUOTES, 'UTF-8');
            $safeErrFile         = htmlspecialchars($relativeFile ?: $errFile, ENT_NOQUOTES, 'UTF-8');
            $safeData            = htmlspecialchars($data, ENT_NOQUOTES, 'UTF-8');

            $message  = "{$headerTitle}\n";
            $message .= "━━━━━━━━━━━━━━━━━━━━━━\n\n";
            $message .= "📌 <b>Ứng dụng:</b> {$appName}\n";
            $message .= "⚙️ <b>Thao tác:</b> <code>{$operation}</code>\n";
            if ($url !== 'N/A') {
                $message .= "🌐 <b>URL:</b> <code>{$safeUrl}</code>\n";
            }
            if ($route !== 'N/A') {
                $message .= "🛣️ <b>Route:</b> <code>{$safeRoute}</code>\n";
            }
            $message .= "\n";

            $message .= "{$labelContent} {$safeErrMsg}\n";
            if ($safeErrFile && $safeErrFile !== 'N/A') {
                $message .= "📍 <b>Vị trí:</b> <code>{$safeErrFile}:{$errLine}</code>\n";
            }

            if ($data && $data !== '[]' && $data !== '{}') {
                $message .= "\n📋 <b>Dữ liệu kèm theo:</b>\n<pre>{$safeData}</pre>\n";
            }

            $message .= "\n🛠 <b>Copy nhanh để Fix (1-Tap):</b>\n";
            $message .= "<code>{$safeQuickFixPrompt}</code>\n";

            $message .= "\n📋 <b>Context JSON:</b>\n";
            $message .= "<pre><code class=\"language-json\">{$safeCopyContextJson}</code></pre>\n";

            $message .= "\n━━━━━━━━━━━━━━━━━━━━━━\n";
            $message .= "🕐 <b>Thời gian:</b> " . now()->format('H:i:s d/m/Y') . "\n";
            $message .= "{$footerText}";

            Http::timeout(10)->post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id'                  => $chatId,
                'text'                     => $message,
                'parse_mode'               => 'HTML',
                'disable_web_page_preview' => true,
            ]);
        } catch (\Throwable $e) {
            Log::error('❌ Lỗi gửi báo cáo qua BugReporter: ' . $e->getMessage());
        }
    }
}
