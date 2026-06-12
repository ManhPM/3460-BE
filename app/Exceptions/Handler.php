<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Illuminate\Validation\ValidationException;
use App\Exceptions\FlashSaleExceededException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register()
    {
        $this->reportable(function (Throwable $e) {
            // 🚫 Skip HttpException (abort) và ValidationException - đã được handle
            if ($e instanceof HttpException || $e instanceof ValidationException) {
                return false;
            }

            $data = collect(request()->except([
                'password',
                'password_confirmation',
                'new_password',
                'new_password_confirmation',
                'old_password',
                '_token',
                '_method'
            ]))->map(function ($value, $key) {
                // Loại bỏ base64 data (image/video)
                if (is_string($value) && preg_match('/^data:(image|video)\/[a-zA-Z]+;base64,/', $value)) {
                    return '[BASE64_DATA_REMOVED]';
                }
                return $value;
            })->toArray();

            // 🗓 Tạo file log theo ngày
            $date = now()->format('d-m-Y');
            $logFile = storage_path("logs/{$date}.log");

            // 🕒 Thời gian hiện tại
            $timestamp = now()->format('Y-m-d H:i:s');

            // 🌍 Environment name (ví dụ: local, production, staging)
            $env = app()->environment();

            // 🧩 Nội dung JSON
            $json = json_encode([
                'route' => request()->route()?->getName() ?? 'unknown',
                'url' => request()->fullUrl(),
                'method' => request()->method(),
                'data' => $data,
                'exception' => [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                ]
            ], JSON_UNESCAPED_UNICODE);

            // 🪵 Format log giống hệt Laravel:
            // [2025-10-09 13:45:27] local.ERROR: [EXCEPTION FAILED] {...}
            $logLine = "[{$timestamp}] {$env}.ERROR: [EXCEPTION FAILED] {$json}" . PHP_EOL;

            // 💾 Ghi vào file theo ngày
            file_put_contents($logFile, $logLine, FILE_APPEND);

            return false;
        });
        $this->renderable(function (MethodNotAllowedHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 404,
                    'message' => __('method_not_allowed')
                ], 404);
            }
        });
        $this->renderable(function (NotFoundHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 404,
                    'message' => __('not_found_route')
                ], 404);
            }
        });
        $this->renderable(function (FlashSaleExceededException $e, $request) {
            if ($request->is('api/*')) {
                return $e->render();
            }
        });
        $this->renderable(function (HttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 400,
                    'message' => $e->getMessage()
                ], 400);
            }
        });
        $this->renderable(function (ModelNotFoundException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 404,
                    'message' => __('not_found_data')
                ], 404);
            }
        });
        $this->renderable(function (TooManyRequestsHttpException $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status' => 429,
                    'message' => __('too_many_requests')
                ], 429);
            }
        });
    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        // Cho API routes, luôn trả về JSON
        if ($request->is('api/*')) {
            return response()->json([
                'status' => 401,
                'message' => __('unauthenticated')
            ], 401);
        }

        // Cho web routes, redirect về trang chủ
        if ($request->expectsJson()) {
            return redirect()->guest('/');
        }

        return redirect()->guest('/');
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof ValidationException) {
            return parent::render($request, $exception);
        }

        return parent::render($request, $exception);
    }
}
