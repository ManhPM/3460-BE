# Error Handling Rules

## Admin Controller — KHÔNG tự try/catch!

`AdminResponse` trait (đã use trong base Controller) xử lý TẤT CẢ:
- `DB::beginTransaction()` / `commit()` / `rollback()`
- `try/catch(Exception)`
- Log lỗi qua `logOperationFailure()`
- Flash message `success`/`error`
- `withInput()` giữ form data

```php
// ✅ ĐÚNG — Controller chỉ gọi handleXxxResponse
public function store({Module}Request $request)
{
    return $this->handleStoreResponse($request, function ($request) {
        return $this->service->store($request);
    }, $this->route['edit']);
}

// ❌ SAI — KHÔNG viết try/catch trong Admin Controller
public function store({Module}Request $request)
{
    try {
        $response = $this->service->store($request);
        // ...
    } catch (\Throwable $th) { ... }
}
```

**Service cũng KHÔNG cần try/catch** — exception sẽ bubble lên AdminResponse.

### API Response Error Pattern
```php
// API Controller
public function store(Request $request)
{
    try {
        $result = $this->service->store($request);
        return response()->json([
            'status'  => 200,
            'message' => __('success'),
            'data'    => new {Module}Resource($result),
        ], 200);
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'status'  => 422,
            'message' => __('validationFailed'),
            'message_validate' => $e->errors(),
        ], 422);
    } catch (\Throwable $th) {
        return response()->json([
            'status'  => 500,
            'message' => __('fail'),
            'error'   => config('app.debug') ? $th->getMessage() : null,
        ], 500);
    }
}
```

## HTTP Status Code chuẩn

| Code | Ý nghĩa | Khi nào dùng |
|---|---|---|
| `200` | Success | CRUD thành công |
| `400` | Bad Request | Input sai logic |
| `401` | Unauthorized | Token hết hạn / chưa login |
| `403` | Forbidden | Không có quyền |
| `404` | Not Found | Resource không tồn tại |
| `422` | Unprocessable Entity | Validation fail (Laravel) |
| `429` | Too Many Requests | Rate limit |
| `500` | Internal Server Error | Server crash |

## Quy tắc error handling

1. **Admin controller**: dùng `back()->with('success'/'error')` cho flash message
2. **API controller**: LUÔN wrap trong `try/catch(\Throwable)` 
3. **Validation**: Dùng `FormRequest`, Laravel tự trả 422 cho API
4. **Service**: Catch ở tầng service, controller chỉ check kết quả
5. **KHÔNG bao giờ** expose stack trace ở production (`config('app.debug')` guard)
6. **Logging**: Dùng `\Log::error()` cho server errors trong catch block
7. **Validation errors**: Trả về `message_validate` key cho Flutter parse

## Middleware Error Classes

| Exception | Xử lý |
|---|---|
| `ModelNotFoundException` | Return 404 |
| `AuthenticationException` | Redirect login (Admin) / Return 401 (API) |
| `AuthorizationException` | Return 403 |
| `ValidationException` | Return 422 + `message_validate` |
| `\Throwable` | Log + Return 500 |
