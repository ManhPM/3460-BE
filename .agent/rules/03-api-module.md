---
trigger: always_on
---

# API V1 Module Architecture

## Cấu trúc thư mục

```
app/Api/V1/
├── Http/
│   ├── Controllers/{Module}/{Module}Controller.php
│   ├── Requests/{Module}/
│   │   └── {Action}Request.php        # Tách riêng từng action
│   └── Resources/{Module}/
│       ├── {Module}Resource.php       # JSON Resource (single item)
│       └── All{Module}Resource.php    # ResourceCollection (danh sách, phân trang)
├── Repositories/{Module}/
│   ├── {Module}RepositoryInterface.php
│   └── {Module}Repository.php
├── Services/{Module}/
│   ├── {Module}ServiceInterface.php
│   └── {Module}Service.php
└── Providers/
    ├── RepositoryServiceProvider.php
    └── ServiceServiceProvider.php
```

## 1. API Controller

- **Extends:** `App\Admin\Http\Controllers\Controller` — kế thừa cả `AdminResponse` + `ApiResponse` traits
- **CRUD:** Dùng `handleApiResponse()` từ `ApiResponse` trait — KHÔNG tự try/catch

```php
<?php
namespace App\Api\V1\Http\Controllers\{Module};

use App\Admin\Http\Controllers\Controller;  // ⚠️ Extends từ Admin Controller
use App\Api\V1\Repositories\{Module}\{Module}RepositoryInterface;
use App\Api\V1\Services\{Module}\{Module}ServiceInterface;
use App\Api\V1\Http\Resources\{Module}\{Module}Resource;
use App\Api\V1\Http\Resources\{Module}\All{Module}Resource;

class {Module}Controller extends Controller
{
    public function __construct(
        {Module}RepositoryInterface $repository,
        {Module}ServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    // ⛔ CRUD — dùng handleApiResponse, KHÔNG tự try/catch!
    public function store({Module}Request $request)
    {
        return $this->handleApiResponse(
            function () use ($request) {
                return $this->service->store($request);
            },
            function ($response) {
                return new {Module}Resource($response);
            }
        );
    }

    public function update({Module}Request $request)
    {
        return $this->handleApiResponse(
            function () use ($request) {
                return $this->service->update($request);
            },
            function ($response) {
                return new {Module}Resource($response);
            }
        );
    }

    // Delete — không cần transform response
    public function delete($id)
    {
        return $this->handleApiResponse(
            function () use ($id) {
                return $this->service->delete($id);
            },
        );
    }

    // Danh sách — PHẢI có phân trang (xem Section 6)
    public function index({Module}Request $request)
    {
        $data = $request->validated();
        $items = $this->repository->paginate($data);
        $items = new All{Module}Resource($items);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $items
        ], 200);
    }
}
```

## ⛔ ApiResponse Trait (QUAN TRỌNG — KHÔNG SỬA, CHỈ DÙNG)

Trait `ApiResponse` tại `app/Traits/ApiResponse.php` cung cấp:

### `handleApiResponse(Closure $storeFunction, ?Closure $transformResponse, ...)`
- `DB::beginTransaction()`
- Gọi `$storeFunction()`
- Nếu truthy → `DB::commit()` → `jsonResponseSuccess(data)` (200)
- Nếu falsy → `DB::rollback()` → `jsonResponseError(message)` (400)
- Catch `HttpException` → rollback + trả status code + message từ `abort()`
- Catch `Exception` → rollback + log + trả message (400) hoặc "INTERNAL SERVER ERROR"

### Helper methods:
- `jsonResponseSuccess($data, $message, $status)` — `{ status, message, data }`
- `jsonResponseError($message, $status, $errorData)` — `{ status, message, errors }`
- `jsonResponseNotFound($message)` — 404

### `logApiOperationFailure(string $operation, ?\Throwable $exception)`
- Log vào daily file `laravel-yyyy-mm-dd.log`
- Lọc sensitive data (password, _token, string > 500 chars)

> ⛔ **TUYỆT ĐỐI KHÔNG** viết `try/catch` trong API Controller cho store/update/delete!
> `ApiResponse` trait đã handle:
> - `DB::beginTransaction()` / `commit()` / `rollback()`
> - `try/catch(HttpException)` + `try/catch(Exception)`
> - Logging (`logApiOperationFailure`)
> - JSON response format chuẩn
>
> **Service cũng KHÔNG cần try/catch** — exception bubble lên ApiResponse trait.

## 2. API Resource (JSON Transform)

### Single Resource (chi tiết 1 item)
```php
<?php
namespace App\Api\V1\Http\Resources\{Module};
use Illuminate\Http\Resources\Json\JsonResource;

class {Module}Resource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            // map fields...
        ];
    }
}
```

### ResourceCollection (danh sách, có phân trang)
```php
<?php
namespace App\Api\V1\Http\Resources\{Module};
use Illuminate\Http\Resources\Json\ResourceCollection;

class All{Module}Resource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return new {Module}Resource($item);
        });
    }
}
```

## 3. API Repository Interface

```php
<?php
namespace App\Api\V1\Repositories\{Module};

interface {Module}RepositoryInterface
{
    public function find($id);
    public function findByKey($key, $value);
    public function findOrFail($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function getQueryBuilder();
    public function paginate(array $filters);  // ⚠️ BẮT BUỘC cho API danh sách
}
```

## 4. API Service Interface

```php
<?php
namespace App\Api\V1\Services\{Module};
use Illuminate\Http\Request;

interface {Module}ServiceInterface
{
    public function store(Request $request);
    public function update(Request $request);
    public function delete($id);
}
```

## 5. API Response Format chuẩn

```php
// Success — single item
return response()->json([
    'status' => 200,
    'message' => __('success'),
    'data' => new {Module}Resource($result)
], 200);

// Error
return response()->json([
    'status' => 400,
    'message' => __('fail')
], 400);

// Server error  
return response()->json([
    'status' => 500,
    'message' => __('fail'),
    'error' => $th->getMessage()
], 500);
```

---

## ⛔ 6. Phân trang — BẮT BUỘC cho API lấy danh sách

> **MỌI API trả về danh sách** (list/index) đều **PHẢI có phân trang**.
> Không bao giờ dùng `->get()` rồi trả nguyên mảng cho API danh sách.

### 2 Pattern phân trang trong dự án:

---

### Pattern A: `simplePaginate()` — Dùng cho list dài, infinite scroll (KHUYẾN NGHỊ)

`simplePaginate` chỉ có `next_page_url` / `prev_page_url`, **KHÔNG có `total`** → performance tốt hơn.

**Repository:**
```php
public function paginate(array $filters)
{
    $limit = isset($filters['limit']) ? max(1, $filters['limit']) : 10;
    
    $query = $this->model->orderBy('id', 'desc');

    // Apply filters...
    if (isset($filters['status']) && !is_null($filters['status'])) {
        $query->where('status', $filters['status']);
    }
    
    $this->instance = $query->simplePaginate($limit);
    return $this->instance;
}
```

**Controller:**
```php
public function index({Module}Request $request)
{
    $data = $request->validated();
    $items = $this->repository->paginate($data);
    $items = new All{Module}Resource($items);

    return response()->json([
        'status' => 200,
        'message' => __('success'),
        'data' => $items
    ], 200);
}
```

**Response tự động có:**
```json
{
    "status": 200,
    "message": "success",
    "data": [ ... ],  // items
    "links": {
        "first": "...?page=1",
        "last": null,
        "prev": null,
        "next": "...?page=2"
    },
    "meta": {
        "current_page": 1,
        "from": 1,
        "path": "...",
        "per_page": 10,
        "to": 10
    }
}
```

> ⚠️ `ResourceCollection` + `simplePaginate` = Laravel **tự động** thêm `links` + `meta` vào response.

---

### Pattern B: `paginate()` — Dùng khi cần `total` + `last_page` (ví dụ: UI hiện tổng pages)

**Repository:**
```php
public function paginate(array $filters)
{
    $limit = isset($filters['limit']) ? max(1, $filters['limit']) : 10;
    
    $query = $this->model->orderBy('id', 'desc');
    // Apply filters...
    
    return $query->paginate($limit);
}
```

**Controller tự gắn meta:**
```php
public function index(Request $request)
{
    $limit = (int) $request->query('limit', 10);
    $items = $this->repository->getItems($limit);
    
    return response()->json([
        'status' => 200,
        'message' => __('success'),
        'data' => {Module}Resource::collection($items),
        'meta' => [
            'current_page' => $items->currentPage(),
            'last_page'    => $items->lastPage(),
            'per_page'     => $items->perPage(),
            'total'        => $items->total(),
        ],
    ]);
}
```

---

### ⚠️ Quy tắc phân trang

| Quy tắc | Mô tả |
|---|---|
| **BẮT BUỘC** | Mọi API danh sách phải có phân trang |
| **Default limit** | `10` hoặc `15` — lấy từ `$filters['limit']` hoặc `$request->query('limit', 10)` |
| **Max limit** | Nên validate `max(1, min($filters['limit'], 100))` tránh client gửi limit quá lớn |
| **Ưu tiên** | `simplePaginate()` cho mobile/infinite scroll, `paginate()` khi UI cần tổng pages |
| **Query params** | `?page=1&limit=10&status=active` — page auto-handle bởi Laravel |
| **OrderBy** | Luôn có `orderBy('id', 'desc')` trước khi paginate |
| **Filter + Paginate** | Apply filter TRƯỚC → paginate() SAU |

### Request Validation cho filters:
```php
<?php
namespace App\Api\V1\Http\Requests\{Module};
use App\Admin\Http\Requests\BaseRequest;

class {Module}Request extends BaseRequest
{
    protected function methodGet()
    {
        return [
            'page'   => ['nullable', 'integer', 'min:1'],
            'limit'  => ['nullable', 'integer', 'min:1', 'max:100'],
            'status' => ['nullable', 'integer'],
            // Thêm filters khác...
        ];
    }
}
```
