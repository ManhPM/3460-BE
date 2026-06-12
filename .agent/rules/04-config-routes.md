# Config Files & Routes

## 1. DataTable Columns (`config/datatables_columns.php`)

```php
'{module_snake}' => [
    'DT_RowIndex' => [
        'title' => 'STT', 'orderable' => false,
        'searchable' => false, 'width' => '150px',
        'addClass' => 'text-center align-middle'
    ],
    'name' => [
        'title' => 'name', 'orderable' => false,
        'width' => '150px', 'addClass' => 'text-center align-middle'
    ],
    'action' => [
        'title' => 'action', 'orderable' => false,
        'exportable' => false, 'printable' => false,
        'addClass' => 'text-center align-middle'
    ],
],
```

## 2. Admin Sidebar (`config/admin_sidebar.php`)

```php
[
    'title' => '{module_snake}_management',
    'routeName' => 'admin.{module_snake}.index',
    'icon' => '<i class="ti ti-{icon}"></i>',
    'roles' => [],
    'permissions' => ['create{Module}', 'view{Module}', 'update{Module}', 'delete{Module}'],
    'sub' => []
],
```

**Quy tắc:** Icon dùng Tabler Icons (`ti ti-*`), permissions 4 quyền CRUD

## 3. Admin Routes (`routes/admin.php`)

```php
//***** -- {Module} -- ******* //
Route::prefix('/{module-kebab}')->as('{module_snake}.')->group(function () {
    Route::controller(\App\Admin\Http\Controllers\{Module}\{Module}Controller::class)->group(function () {

        Route::group(['middleware' => ['permission:create{Module}', 'auth:admin']], function () {
            Route::get('/add', 'create')->name('create');
            Route::post('/add', 'store')->name('store');
        });
        Route::group(['middleware' => ['permission:view{Module}', 'auth:admin']], function () {
            Route::get('/', 'index')->name('index');
            Route::get('/edit/{id}', 'edit')->name('edit');
        });
        Route::group(['middleware' => ['permission:update{Module}', 'auth:admin']], function () {
            Route::put('/edit', 'update')->name('update');
        });
        Route::group(['middleware' => ['permission:delete{Module}', 'auth:admin']], function () {
            Route::delete('/delete/{id}', 'delete')->name('delete');
        });
    });
});
//***** -- {Module} -- ******* //
```

**Quy tắc Route:**
- URL prefix: **kebab-case** (`/customers`, `/order-reports`)
- Route alias: **snake_case** (`customer.`, `order_report.`)
- Mỗi nhóm middleware: `permission:{action}{Module}` + `auth:admin`
- URL create/store: `/add`
- URL edit/update: `/edit`, `/edit/{id}`
- URL delete: `/delete/{id}`
