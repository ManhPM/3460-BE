<?php

namespace App\Traits;

use App\Admin\Support\Breadcrumb\Breadcrumb;
use Closure;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Validation\ValidationException;

trait AdminResponse
{
    public function renderView(string $view, Breadcrumb $breadcrumbs, array $data = [])
    {
        return view($view, array_merge($data, ['breadcrumbs' => $breadcrumbs]));
    }

    public function handleStoreResponse(Request $request, Closure $storeFunction, ?string $editRoute = null): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $storeFunction($request);

            if ($response) {
                DB::commit();
                return $editRoute ? to_route($editRoute, ['id' => $response->id])->with('success', __('success')) : back()->with('success', __('success'));
            }

            DB::rollback();
            $this->logOperationFailure('STORE', $request);

            return back()->with('error', __('fail'))->withInput();
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('STORE', $request, $e);
            return back()->with('error', env('APP_DEBUG') ? $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine() : __('server_error_please_try_again'))->withInput();
        }
    }

    /**
     * Generate a response for updating a resource with transaction handling.
     *
     * @param Request $request The current request instance.
     * @param Closure $updateFunction The closure function that performs the update operation.
     * @return RedirectResponse
     */
    public function handleUpdateResponse(Request $request, Closure $updateFunction): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $updateFunction($request);

            if ($response) {
                DB::commit();
                return back()->with('success', __('success'));
            }

            DB::rollback();
            $this->logOperationFailure('UPDATE', $request);

            return back()->with('error', __('fail'))->withInput();
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('UPDATE', $request, $e);
            return back()->with('error', env('APP_DEBUG') ? $e->getMessage() . ' - ' . $e->getFile() . ' - ' . $e->getLine() : __('server_error_please_try_again'))->withInput();
        }
    }

    /**
     * Generate a response for deleting a resource with transaction handling.
     *
     * @param mixed $id The id of the resource to delete.
     * @param Closure $deleteFunction The closure function that performs the delete operation.
     * @return RedirectResponse
     */
    public function handleDeleteResponse($id, Closure $deleteFunction, ?string $indexRoute = null): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $deleteFunction($id);

            if ($response) {
                DB::commit();
                return $indexRoute ? to_route($indexRoute)->with('success', __('success')) : back()->with('success', __('success'));
            }

            DB::rollback();
            $this->logOperationFailure('DELETE');

            return back()->with('error', __('fail'));
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('DELETE', null, $e);
            return back()->with('error', $e->getMessage());
        }
    }

    public function handleStoreResponseWithCustomParam($paramValue, $paramName, Request $request, Closure $storeFunction, string $indexRoute): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $storeFunction($request);

            if ($response) {
                DB::commit();
                return to_route($indexRoute, [$paramName => $paramValue])->with('success', __('success'));
            }

            DB::rollback();
            $this->logOperationFailure('STORE', $request);

            return back()->with('error', __('fail'))->withInput();
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('STORE', $request, $e);
            return back()->with('error', env('APP_DEBUG') ? $e->getMessage() : __('server_error_please_try_again'))->withInput();
        }
    }

    public function handleUpdateResponseWithCustomParam($paramValue, $paramName, Request $request, Closure $updateFunction, string $indexRoute): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $updateFunction($request);

            if ($response) {
                DB::commit();
                return to_route($indexRoute, [$paramName => $paramValue])->with('success', __('success'));
            }

            DB::rollback();
            $this->logOperationFailure('UPDATE', $request);

            return back()->with('error', __('fail'))->withInput();
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('UPDATE', $request, $e);
            return back()->with('error', env('APP_DEBUG') ? $e->getMessage() : __('server_error_please_try_again'))->withInput();
        }
    }

    public function handleDeleteResponseWithCustomParam($id, $paramValue, $paramName, Closure $deleteFunction, string $indexRoute): RedirectResponse
    {
        DB::beginTransaction();

        try {
            $response = $deleteFunction($id);

            if ($response) {
                DB::commit();
                return to_route($indexRoute, [$paramName => $paramValue])->with('success', __('success'));
            }

            DB::rollback();
            $this->logOperationFailure('DELETE');

            return back()->with('error', __('fail'));
        } catch (Exception $e) {
            DB::rollback();
            $this->logOperationFailure('DELETE', null, $e);
            return back()->with('error', $e->getMessage());
        }
    }


    /**
     * Log operation failure with structured context.
     *
     * @param string $operation The operation type (STORE, UPDATE, DELETE)
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
                // Loại bỏ base64 data (image/video)
                if (is_string($value) && preg_match('/^data:(image|video)\/[a-zA-Z]+;base64,/', $value)) {
                    return '[BASE64_DATA_REMOVED]';
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
        $logLine = "[{$timestamp}] {$env}.ERROR: [{$operation} FAILED] {$json}" . PHP_EOL;

        // 💾 Ghi vào file theo ngày
        file_put_contents($logFile, $logLine, FILE_APPEND);
    }
}
