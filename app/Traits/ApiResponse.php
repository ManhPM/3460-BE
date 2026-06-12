<?php

namespace App\Traits;

use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;

trait ApiResponse
{
    /**
     * Generate a JSON response for storing a resource with transaction handling.
     *
     * @param Request $request The current request instance.
     * @param Closure $storeFunction The closure function that performs the store operation.
     * @param string|null $successMessage Custom success message (optional).
     * @param int $statusCode HTTP status code for success (default: 201).
     * @return JsonResponse
     */
    public function handleStoreResponse(
        Request $request,
        Closure $storeFunction,
        ?string $successMessage = null,
        int $statusCode = 201
    ): JsonResponse {
        DB::beginTransaction();

        try {
            $response = $storeFunction($request);

            if ($response) {
                DB::commit();
                return response()->json([
                    'status' => $statusCode,
                    'message' => $successMessage ?? __('success'),
                    'data' => $response,
                ], $statusCode);
            }

            DB::rollback();
            $this->logOperationFailure('STORE', $request);

            return response()->json([
                'status' => 500,
                'message' => __('fail'),
                'data' => null,
            ], 500);
        } catch (ValidationException $e) {
            DB::rollback();
            $errors = $e->errors();
            $firstError = !empty($errors) ? reset($errors)[0] : __('Dữ liệu không hợp lệ.');
            return response()->json([
                'status' => 400,
                'message' => $firstError,
                'errors' => $errors,
                'data' => null,
            ], 400);
        } catch (HttpException $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage() ?: __('fail'),
                'data' => null,
            ], $e->getStatusCode());
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('STORE', $request, $e);

            return response()->json([
                'status' => 500,
                'message' => env('APP_DEBUG')
                    ? $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine()
                    : __('Đã có lỗi xảy ra trên máy chủ. Vui lòng thử lại sau.'),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Generate a JSON response for updating a resource with transaction handling.
     *
     * @param Request $request The current request instance.
     * @param Closure $updateFunction The closure function that performs the update operation.
     * @param string|null $successMessage Custom success message (optional).
     * @param int $statusCode HTTP status code for success (default: 200).
     * @return JsonResponse
     */
    public function handleUpdateResponse(
        Request $request,
        Closure $updateFunction,
        ?string $successMessage = null,
        int $statusCode = 200
    ): JsonResponse {
        DB::beginTransaction();

        try {
            $response = $updateFunction($request);

            if ($response) {
                DB::commit();
                return response()->json([
                    'status' => $statusCode,
                    'message' => $successMessage ?? __('success'),
                    'data' => $response,
                ], $statusCode);
            }

            DB::rollback();
            $this->logOperationFailure('UPDATE', $request);

            return response()->json([
                'status' => 500,
                'message' => __('fail'),
                'data' => null,
            ], 500);
        } catch (ValidationException $e) {
            DB::rollback();
            $errors = $e->errors();
            $firstError = !empty($errors) ? reset($errors)[0] : __('data_invalid');
            return response()->json([
                'status' => 400,
                'message' => $firstError,
                'errors' => $errors,
                'data' => null,
            ], 400);
        } catch (HttpException $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage() ?: __('fail'),
                'data' => null,
            ], $e->getStatusCode());
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('UPDATE', $request, $e);

            return response()->json([
                'status' => 500,
                'message' => env('APP_DEBUG')
                    ? $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine()
                    : __('server_error_please_try_again'),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Generate a JSON response for deleting a resource with transaction handling.
     *
     * @param mixed $id The id of the resource to delete.
     * @param Closure $deleteFunction The closure function that performs the delete operation.
     * @param string|null $successMessage Custom success message (optional).
     * @param int $statusCode HTTP status code for success (default: 200).
     * @return JsonResponse
     */
    public function handleDeleteResponse(
        $id,
        Closure $deleteFunction,
        ?string $successMessage = null,
        int $statusCode = 200
    ): JsonResponse {
        DB::beginTransaction();

        try {
            $response = $deleteFunction($id);

            if ($response) {
                DB::commit();
                return response()->json([
                    'status' => $statusCode,
                    'message' => $successMessage ?? __('success'),
                    'data' => null,
                ], $statusCode);
            }

            DB::rollback();
            $this->logOperationFailure('DELETE');

            return response()->json([
                'status' => 500,
                'message' => __('fail'),
                'data' => null,
            ], 500);
        } catch (HttpException $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage() ?: __('fail'),
                'data' => null,
            ], $e->getStatusCode());
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('DELETE', null, $e);

            return response()->json([
                'status' => 500,
                'message' => env('APP_DEBUG')
                    ? $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine()
                    : __('server_error_please_try_again'),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Generic method to handle any operation with transaction and JSON response.
     *
     * @param Closure $operationFunction The closure function that performs the operation.
     * @param string|null $successMessage Custom success message (optional).
     * @param int $statusCode HTTP status code for success (default: 200).
     * @param Request|null $request The request instance (optional, for logging).
     * @return JsonResponse
     */
    public function handleResponse(
        Closure $operationFunction,
        ?string $successMessage = null,
        int $statusCode = 200,
        ?Request $request = null
    ): JsonResponse {
        DB::beginTransaction();

        try {
            $response = $operationFunction();

            DB::commit();
            return response()->json([
                'status' => $statusCode,
                'message' => $successMessage ?? __('success'),
                'data' => $response,
            ], $statusCode);
        } catch (ValidationException $e) {
            DB::rollback();
            return response()->json([
                'status' => 422,
                'message' => __('data_invalid'),
                'errors' => $e->errors(),
                'data' => null,
            ], 422);
        } catch (HttpException $e) {
            DB::rollback();
            return response()->json([
                'status' => $e->getStatusCode(),
                'message' => $e->getMessage() ?: __('server_error_please_try_again'),
                'data' => null,
            ], $e->getStatusCode());
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('OPERATION', $request ?? request(), $e);

            return response()->json([
                'status' => 500,
                'message' => env('APP_DEBUG')
                    ? $e->getMessage() . ' - ' . $e->getFile() . ':' . $e->getLine()
                    : __('Đã có lỗi xảy ra trên máy chủ. Vui lòng thử lại sau.'),
                'data' => null,
            ], 500);
        }
    }

    /**
     * Log operation failure with structured context.
     *
     * @param string $operation The operation type (STORE, UPDATE, DELETE, OPERATION)
     * @param Request|null $request The request instance (optional)
     * @param \Throwable|null $exception Exception object if available
     * @return void
     */
    protected function logOperationFailure(
        string $operation,
        ?Request $request = null,
        ?\Throwable $exception = null
    ): void {
        // 🚫 Skip HttpException (abort) và ValidationException - đã được handle
        if ($exception instanceof HttpException || $exception instanceof ValidationException) {
            return;
        }

        $routeName = Route::currentRouteName();
        $url = $request ? $request->fullUrl() : request()->fullUrl();
        $method = $request ? $request->method() : request()->method();

        // Lọc dữ liệu nhạy cảm
        $data = [];
        if ($request) {
            $data = collect($request->except([
                'password',
                'password_confirmation',
                'new_password',
                'new_password_confirmation',
                'old_password',
                '_token',
                '_method',
            ]))->map(function ($value, $key) {
                // Loại bỏ base64 data (image/video) - có hoặc không có prefix
                if (is_string($value)) {
                    // Kiểm tra base64 có prefix data:image hoặc data:video
                    if (preg_match('/^data:(image|video)\/[a-zA-Z]+;base64,/', $value)) {
                        return '[BASE64_DATA_REMOVED]';
                    }
                    // Kiểm tra string quá dài (có thể là base64 không có prefix)
                    // hoặc bất kỳ string nào dài hơn 500 ký tự
                    if (strlen($value) > 500) {
                        // Kiểm tra xem có phải base64 không (chỉ chứa A-Z, a-z, 0-9, +, /, =)
                        if (preg_match('/^[A-Za-z0-9+\/=]+$/', $value)) {
                            return '[BASE64_DATA_REMOVED]';
                        }
                        // Nếu không phải base64 nhưng quá dài, cắt ngắn lại
                        return substr($value, 0, 500) . '... [TRUNCATED]';
                    }
                }
                return $value;
            })->toArray();
        }

        // 🗓 Tạo file log theo ngày
        $date = now()->format('d-m-Y');
        $logFile = storage_path("logs/{$date}.log");

        // 🕒 Thời gian hiện tại
        $timestamp = now()->format('Y-m-d H:i:s');

        // 🌍 Environment name (ví dụ: local, production, staging)
        $env = app()->environment();

        // 🧩 Chuẩn bị context log
        $context = [
            'route' => $routeName,
            'url' => $url,
            'method' => $method,
            'data' => $data,
        ];

        if ($exception) {
            $context['exception'] = [
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ];
        }

        // 🧩 Nội dung JSON
        $json = json_encode($context, JSON_UNESCAPED_UNICODE);

        // 🪵 Format log giống hệt Laravel:
        // [2025-10-09 13:45:27] local.ERROR: [OPERATION FAILED] {...}
        $logLine = "[{$timestamp}] {$env}.ERROR: [API {$operation} FAILED] {$json}" . PHP_EOL;

        // 💾 Ghi vào file theo ngày
        file_put_contents($logFile, $logLine, FILE_APPEND);
    }
}
