# ⛔ Provider Registration (CRITICAL)

> **QUAN TRỌNG:** Nếu thiếu đăng ký trong Provider, dependency injection sẽ FAIL → lỗi runtime!
> Đây là lỗi phổ biến nhất khi implement module mới.

## Admin Providers

### Repository: `app/Admin/Providers/RepositoryServiceProvider.php`
```php
// Thêm vào mảng $repositories:
'App\Admin\Repositories\{Module}\{Module}RepositoryInterface' => 'App\Admin\Repositories\{Module}\{Module}Repository',
```

### Service: `app/Admin/Providers/ServiceServiceProvider.php`
```php
// Thêm vào mảng $services:
'App\Admin\Services\{Module}\{Module}ServiceInterface' => 'App\Admin\Services\{Module}\{Module}Service',
```

## API V1 Providers

### Repository: `app/Api/V1/Providers/RepositoryServiceProvider.php`
```php
// Thêm vào mảng $repositories:
'App\Api\V1\Repositories\{Module}\{Module}RepositoryInterface' => 'App\Api\V1\Repositories\{Module}\{Module}Repository',
```

### Service: `app/Api/V1/Providers/ServiceServiceProvider.php`
```php
// Thêm vào mảng $services:
'App\Api\V1\Services\{Module}\{Module}ServiceInterface' => 'App\Api\V1\Services\{Module}\{Module}Service',
```

## Cách đăng ký
Cả 4 provider đều dùng pattern giống nhau: loop qua mảng property rồi `$this->app->singleton()`:
```php
public function register()
{
    foreach ($this->repositories as $interface => $implement) {
        $this->app->singleton($interface, $implement);
    }
}
```
