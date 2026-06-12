---
trigger: always_on
---

# Admin Module Architecture

## Cấu trúc thư mục bắt buộc

```
app/Admin/
├── Http/
│   ├── Controllers/{Module}/{Module}Controller.php
│   └── Requests/{Module}/{Module}Request.php
├── Repositories/{Module}/
│   ├── {Module}RepositoryInterface.php
│   └── {Module}Repository.php
├── Services/{Module}/
│   ├── {Module}ServiceInterface.php
│   └── {Module}Service.php
├── DataTables/{Module}/{Module}DataTable.php
└── Traits/
    └── AdminResponse.php          # ⛔ KHÔNG SỬA - trait dùng chung
```

## Inheritance Chain

```
BaseController (setView, setRoute, $view, $route, $crums Breadcrumb)
     ↓ extends
Controller ($repository, $service, use AdminResponse, use ApiResponse)
     ↓ extends
{Module}Controller (override getView(), getRoute())
```

## 1. Controller

- **Namespace:** `App\Admin\Http\Controllers\{Module}`
- **Extends:** `App\Admin\Http\Controllers\Controller`
- **Constructor:** Inject `{Module}RepositoryInterface` + `{Module}ServiceInterface`, gọi `parent::__construct()`
- **Override:** `getView()` trả mảng view paths, `getRoute()` trả mảng route names
- **CRUD Methods:** Dùng `handleStoreResponse`, `handleUpdateResponse`, `handleDeleteResponse` từ `AdminResponse` trait
- **View render:** Dùng `$this->renderView()` từ `AdminResponse` trait

```php
<?php
namespace App\Admin\Http\Controllers\{Module};

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\{Module}\{Module}Request;
use App\Admin\Repositories\{Module}\{Module}RepositoryInterface;
use App\Admin\Services\{Module}\{Module}ServiceInterface;
use App\Admin\DataTables\{Module}\{Module}DataTable;

class {Module}Controller extends Controller
{
    public function __construct(
        {Module}RepositoryInterface $repository,
        {Module}ServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    // ⚠️ Override getView() thay vì set $this->view trực tiếp
    public function getView()
    {
        return [
            'index'  => 'admin.{module_snake}.index',
            'create' => 'admin.{module_snake}.create',
            'edit'   => 'admin.{module_snake}.edit',
        ];
    }

    // ⚠️ Override getRoute() thay vì set $this->route trực tiếp
    public function getRoute()
    {
        return [
            'index'  => 'admin.{module_snake}.index',
            'create' => 'admin.{module_snake}.create',
            'edit'   => 'admin.{module_snake}.edit',
            'delete' => 'admin.{module_snake}.delete',
        ];
    }

    public function index({Module}DataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('{module_snake}_list')),
            'list' => __('{module_snake}_list')
        ]);
    }

    // ⚠️ Dùng renderView() — tự merge breadcrumbs vào data
    public function create()
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('{module_snake}_list'), route($this->route['index']))->add(__('add')),
            [
                'key' => $value,
            ]
        );
    }

    public function edit($id)
    {
        ${module_snake} = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('{module_snake}_list'), route($this->route['index']))->add(__('edit')),
            [
                '{module_snake}' => ${module_snake},
            ]
        );
    }

    // ⛔ CRITICAL: Dùng handleStoreResponse — KHÔNG tự try/catch!
    // AdminResponse trait đã có DB::beginTransaction + try/catch + rollback + log
    public function store({Module}Request $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    // ⛔ CRITICAL: Dùng handleUpdateResponse — KHÔNG tự try/catch!
    public function update({Module}Request $request)
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    // ⛔ CRITICAL: Dùng handleDeleteResponse — KHÔNG tự try/catch!
    public function delete($id)
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
```

## ⛔ AdminResponse Trait (QUAN TRỌNG — KHÔNG SỬA, CHỈ DÙNG)

Trait `AdminResponse` tại `app/Admin/Traits/AdminResponse.php` cung cấp:

### `renderView(string $view, Breadcrumb $breadcrumbs, array $data = [])`
- Merge `$data` + `$breadcrumbs` rồi return `view()`

### `handleStoreResponse(Request $request, Closure $storeFunction, ?string $editRoute = null)`
- `DB::beginTransaction()`
- Gọi `$storeFunction($request)`
- Nếu success → `DB::commit()` → redirect tới `$editRoute` (hoặc `back()`) với flash `success`
- Nếu false → `DB::rollback()` → `back()` với flash `error`
- Catch Exception → `DB::rollback()` → log → `back()` với error message

### `handleUpdateResponse(Request $request, Closure $updateFunction)`
- Tương tự store nhưng luôn `back()` (không redirect)

### `handleDeleteResponse($id, Closure $deleteFunction, ?string $indexRoute = null)`
- Tương tự store, redirect tới `$indexRoute` hoặc `back()`

### `logOperationFailure(string $operation, ?Request $request, ?\Throwable $exception)`
- Log lỗi vào file daily log format Laravel
- Lọc sensitive data (password, base64, _token)
- Skip HttpException và ValidationException

> ⛔ **TUYỆT ĐỐI KHÔNG** viết `try/catch` trong Controller cho store/update/delete!
> `AdminResponse` trait đã handle:
> - `DB::beginTransaction()` / `commit()` / `rollback()`
> - `try/catch(Exception)`
> - Logging (`logOperationFailure`)
> - Flash message (`success`/`error`)
> - `withInput()` để giữ form data khi lỗi

## 2. Service

> ⛔ Service cũng **KHÔNG cần try/catch**!
> Nếu có exception, nó sẽ bubble lên AdminResponse trait xử lý.
> Service chỉ cần throw exception khi có lỗi logic, hoặc return false value khi thất bại.

```php
<?php
namespace App\Admin\Services\{Module};
use App\Admin\Repositories\{Module}\{Module}RepositoryInterface;
use Illuminate\Http\Request;

class {Module}Service implements {Module}ServiceInterface
{
    protected $data;
    protected $repository;

    public function __construct({Module}RepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        // Business logic...
        $record = $this->repository->create($this->data);
        // Relations, sync, etc...
        return $record;  // ← Return truthy = success, falsy = fail
    }

    public function update(Request $request)
    {
        $this->data = $request->validated();
        // Business logic...
        $this->repository->update($this->data['id'], $this->data);
        return 1;  // ← Return truthy = success
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
```

## 3. Repository

**Interface** extends `EloquentRepositoryInterface`:
```php
<?php
namespace App\Admin\Repositories\{Module};
use App\Admin\Repositories\EloquentRepositoryInterface;

interface {Module}RepositoryInterface extends EloquentRepositoryInterface
{
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
}
```

**Implementation** extends `EloquentRepository`:
```php
<?php
namespace App\Admin\Repositories\{Module};
use App\Admin\Repositories\EloquentRepository;
use App\Models\{Module};

class {Module}Repository extends EloquentRepository implements {Module}RepositoryInterface
{
    public function getModel() { return {Module}::class; }
}
```

## 4. Request (Validation)

- **Extends:** `App\Admin\Http\Requests\BaseRequest`
- **Override:** `methodPost()` cho tạo mới, `methodPut()` cho cập nhật

```php
<?php
namespace App\Admin\Http\Requests\{Module};
use App\Admin\Http\Requests\BaseRequest;

class {Module}Request extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }
    protected function methodPut()
    {
        return [
            'id'   => ['required', 'exists:{table},id'],
            'name' => ['required', 'string', 'max:255'],
        ];
    }
}
```

## 5. DataTable

```php
<?php
namespace App\Admin\DataTables\{Module};
use App\Admin\DataTables\BaseDataTable;

class {Module}DataTable extends BaseDataTable
{
    protected $nameTable = '{module_snake}';
    public function __construct()
    {
        parent::__construct();
        $this->setView();
        $this->setColumnSearch();
    }
    // implement: setView, setColumnSearch, setCustomColumns, setCustomEditColumns, setColumnDef
}
```
