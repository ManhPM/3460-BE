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

    /**
     * Trích xuất tối đa 5 frame stack trace thuộc source code ứng dụng (bỏ qua vendor/).
     */
    protected function extractAppTrace(?\Throwable $e): array
    {
        if (!$e) {
            return [];
        }

        $frames = [];
        foreach ($e->getTrace() as $frame) {
            $file = (string) ($frame['file'] ?? '');
            if ($file === '' || str_contains($file, '/vendor/') || str_ends_with($file, 'public/index.php')) {
                continue;
            }

            $relFile = $this->normalizeBugFile($file);
            $line    = $frame['line'] ?? '?';
            $class   = $frame['class'] ?? '';
            $type    = $frame['type'] ?? '';
            $func    = $frame['function'] ?? '';
            $call    = ($class || $func) ? " ({$class}{$type}{$func})" : '';

            $frames[] = "{$relFile}:{$line}{$call}";
            if (count($frames) >= 5) {
                break;
            }
        }

        return $frames;
    }

    /**
     * Chuẩn hóa mảng trace truyền sẵn từ context (ví dụ từ ApiResponse).
     */
    protected function normalizeTraceArray(array $trace): array
    {
        $normalized = [];
        foreach (array_slice($trace, 0, 5) as $item) {
            if (is_string($item)) {
                $normalized[] = $this->normalizeBugFile($item);
            } elseif (is_array($item) && isset($item['file'])) {
                $relFile = $this->normalizeBugFile((string) $item['file']);
                $line    = $item['line'] ?? '?';
                $normalized[] = "{$relFile}:{$line}";
            }
        }
        return $normalized;
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
                    'class' => get_class($e),
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $this->extractAppTrace($e),
                ],
            ]);
        } catch (\Throwable $ex) {
            // Silent fail
        }
    }

    /**
     * Xác định tên thư mục dự án local trên máy Mac (/Applications/XAMPP/xamppfiles/htdocs/{folder})
     * ngay cả khi chạy trên server DirectAdmin (public_html) hoặc thư mục release.
     */
    protected function resolveProjectFolder(): string
    {
        $basePath = str_replace('\\', '/', base_path());

        if (preg_match('#/htdocs/([^/]+(?:/back-end)?)#', $basePath, $m)) {
            return $m[1];
        }

        $folder = basename($basePath);
        if ($folder === 'back-end') {
            $parent = basename(dirname($basePath));
            if (!in_array($parent, ['public_html', 'httpdocs', 'htdocs', 'www', 'html'], true)) {
                return $parent . '/back-end';
            }
        }

        if (
            in_array($folder, ['public_html', 'httpdocs', 'htdocs', 'current', 'html', 'www'], true)
            || str_starts_with($folder, 'release_')
        ) {
            return (string) env('BUG_PROJECT_FOLDER', '3460-BE');
        }

        return $folder;
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
     * Chuẩn hóa đường dẫn file lỗi (bỏ base_path để đồng nhất giữa local và server production).
     */
    protected function normalizeBugFile(string $file): string
    {
        if ($file === '' || $file === 'N/A') {
            return '';
        }

        $normalized = str_replace('\\', '/', $file);
        $basePath   = rtrim(str_replace('\\', '/', base_path()), '/') . '/';

        if (str_starts_with($normalized, $basePath)) {
            return substr($normalized, strlen($basePath));
        }

        $normalized = preg_replace('#^/home/[^/]+/domains/[^/]+/public_html/#', '', $normalized);
        $normalized = preg_replace('#^/var/www/[^/]+/release_[^/]+/#', '', (string) $normalized);
        $normalized = preg_replace('#^/var/www/[^/]+/#', '', (string) $normalized);
        $normalized = preg_replace('#^/Applications/XAMPP/xamppfiles/htdocs/[^/]+/(?:back-end/)?#', '', (string) $normalized);

        return $normalized ?: $file;
    }

    /**
     * Tạo Context JSON rõ ràng, dễ đọc cho AI (Antigravity) để copy nhanh và fix lỗi ngay lập tức.
     * - Mặc định ($pretty = false): JSON 1 dòng gọn gàng để trên Google Sheets chỉ chiếm 1 dòng, bấm vào mới bung rộng.
     * - Khi gửi Telegram ($pretty = true): JSON nhiều dòng dễ nhìn trong khung code.
     */
    protected function buildBugContextJson(
        string $projectFolder,
        string $appName,
        string $operation,
        string $method,
        string $url,
        string $pathOnly,
        string $route,
        string $exceptionClass,
        string $errMsg,
        string $relativeFile,
        $errLine,
        array $payloadData,
        array $trace,
        string $timestamp,
        bool $pretty = false
    ): string {
        $localRoot = "/Applications/XAMPP/xamppfiles/htdocs/{$projectFolder}";
        $localFile = $relativeFile !== ''
            ? "@[{$localRoot}/{$relativeFile}]"
            : "@[{$localRoot}]";
        $fileLine  = $relativeFile !== ''
            ? ($errLine !== '' ? "{$relativeFile}:{$errLine}" : $relativeFile)
            : 'N/A';

        $contextPayload = [
            'fix_file'  => $localFile,
            'project'   => $appName,
            'operation' => $operation,
            'error'     => array_filter([
                'class'    => $exceptionClass !== '' ? $exceptionClass : null,
                'message'  => $errMsg,
                'file'     => $relativeFile ?: 'N/A',
                'line'     => $errLine !== '' ? (is_numeric($errLine) ? (int) $errLine : $errLine) : null,
                'location' => $fileLine,
            ], static fn($v) => $v !== null),
            'request'   => [
                'method'  => $method,
                'url'     => $url,
                'path'    => $pathOnly,
                'route'   => $route,
                'payload' => $payloadData,
            ],
            'trace'     => $trace,
            'timestamp' => $timestamp,
        ];

        if (empty($contextPayload['trace'])) {
            unset($contextPayload['trace']);
        }

        $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | ($pretty ? JSON_PRETTY_PRINT : 0);
        $json  = (string) json_encode($contextPayload, $flags);

        if (strlen($json) > 3000) {
            $contextPayload['request']['payload'] = '[Truncated due to size]';
            $json = (string) json_encode($contextPayload, $flags);
        }

        return $json;
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
            $method         = (string) ($context['method'] ?? (app()->bound('request') ? request()->method() : 'CLI'));
            $url            = (string) ($context['url'] ?? (app()->bound('request') ? request()->fullUrl() : 'N/A'));
            $pathOnly       = $this->normalizeBugPath($url);
            $route          = (string) ($context['route'] ?? (app()->bound('request') ? (request()->route()?->getName() ?? 'N/A') : 'N/A'));
            $exceptionClass = (string) ($context['exception']['class'] ?? '');
            $errMsg         = trim((string) ($context['exception']['message'] ?? ($context['message'] ?? 'N/A')));
            $errFile        = (string) ($context['exception']['file'] ?? ($context['file'] ?? ''));
            $relativeFile   = $this->normalizeBugFile($errFile);
            $errLine        = (string) ($context['exception']['line'] ?? ($context['line'] ?? ''));
            $fileLine       = $relativeFile !== '' ? "{$relativeFile}:{$errLine}" : 'N/A';
            $payloadArray   = is_array($context['data'] ?? null) ? $context['data'] : [];
            $rawTrace       = is_array($context['exception']['trace'] ?? null) ? $context['exception']['trace'] : [];
            $trace          = $this->normalizeTraceArray($rawTrace);

            $dataStr = (string) json_encode($payloadArray, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            if (strlen($dataStr) > 500) {
                $dataStr = substr($dataStr, 0, 500) . '... (truncated)';
            }

            $folder  = $this->resolveProjectFolder();
            $envName = config('app.name') ?: env('APP_NAME', '');
            $appName = ($envName && !in_array(strtolower($envName), ['laravel', 'app'], true))
                ? "{$folder} ({$envName})"
                : $folder;

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

            // JSON 1 dòng gọn gàng cho Google Sheets (hiển thị gọn 1 hàng, click đúp mới mở rộng)
            $contextJsonSheet = $this->buildBugContextJson(
                $folder,
                $appName,
                $operation,
                $method,
                $url,
                $pathOnly,
                $route,
                $exceptionClass,
                $errMsg,
                $relativeFile,
                $errLine,
                $payloadArray,
                $trace,
                $timestamp,
                false
            );

            // JSON format nhiều dòng cho Telegram
            $contextJsonTelegram = $this->buildBugContextJson(
                $folder,
                $appName,
                $operation,
                $method,
                $url,
                $pathOnly,
                $route,
                $exceptionClass,
                $errMsg,
                $relativeFile,
                $errLine,
                $payloadArray,
                $trace,
                $timestamp,
                true
            );

            // 1. Đẩy lỗi duy nhất lên Google Sheets (5 cột gọn: Key | Context JSON | Trạng thái | Dự án | Message lỗi)
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
                                'key'            => $uniqueKey,
                                'unique_key'     => $uniqueKey,
                                'context_json'   => $contextJsonSheet,
                                'status'         => 'Chưa xử lý',
                                'project'        => $appName,
                                'message'        => $errMsg,
                            ]);
                    } catch (\Throwable $sheetEx) {
                        Log::warning('[BugReporter] Không thể đẩy log lên Google Sheets: ' . $sheetEx->getMessage());
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

            $safeContextJson = htmlspecialchars($contextJsonTelegram, ENT_NOQUOTES, 'UTF-8');
            $safeUrl         = htmlspecialchars("{$method} {$url}", ENT_NOQUOTES, 'UTF-8');
            $safeRoute       = htmlspecialchars($route, ENT_NOQUOTES, 'UTF-8');
            $safeErrMsg      = htmlspecialchars($errMsg, ENT_NOQUOTES, 'UTF-8');
            $safeErrFile     = htmlspecialchars($relativeFile ?: $errFile, ENT_NOQUOTES, 'UTF-8');
            $safeData        = htmlspecialchars($dataStr, ENT_NOQUOTES, 'UTF-8');

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

            if ($dataStr && $dataStr !== '[]' && $dataStr !== '{}') {
                $message .= "\n📋 <b>Dữ liệu kèm theo:</b>\n<pre>{$safeData}</pre>\n";
            }

            $message .= "\n📋 <b>Context JSON (Copy nhanh để Fix):</b>\n";
            $message .= "<pre><code class=\"language-json\">{$safeContextJson}</code></pre>\n";

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
