Từ migration (đã có), hãy tạo đầy đủ Admin CRUD theo chuẩn dự án:

Model,DataTable,Controller,Repository,Service,Request,View,Route,admin_sidebar,datatable_columns nhớ là tuyệt đối phải học theo cách tổ chức code đã được tạo sẵn nhất là phần view tôi thấy bạn hay bị sai lắm

-   Tên class không được có dấu hiệu có số nhiều ví dụ DeliveryGoodsTypeRequest => DeliveryGoodTypeRequest bỏ s trong Goods
-   Tạo Model Eloquent (tắt timestamps nếu bảng không có), thêm quan hệ cần thiết.
-   Tạo Admin Repository (interface + implementation) extends EloquentRepository, getModel() trả về Model mới.
-   Tạo Admin Service (interface + implementation) với các hàm store/update/delete; dùng $request->validated().
-   Tạo Admin FormRequest extends App\Admin\Http\Requests\BaseRequest với methodPost/methodPut chuẩn, kiểm tra exists theo namespace model (ví dụ exists:App\Models\ModelName,id).
-   Phần request thay vì exists:App\\Models\\Business,id sẽ là exists:App\Models\Business,id nha ko dùng 2 dấu \\ hãy cố gắng import chúng vào luôn. Phần message của Request soạn giúp tôi luôn các message validate cho rõ ràng rồi bổ sung vào vi.json cấu trúc thì tham khảo các file request khác
-   Tạo Admin Controller kế thừa base Controller, inject Repository + Service, implement các hàm index(create/store/edit/update/delete), index dùng DataTable render với breadcrumbs kèm các view xử lý và cả các view hiển thị có thể tham khảo cùng cấp có thể tham khảo custom filter column.
-   Tạo Admin DataTable extends BaseDataTable, query() dùng repository->getByQueryBuilder([...], [các relations]), setCustomColumns lấy từ config('datatables_columns.<key>'), thêm custom views nếu cần.
-   Đăng ký binding trong: app/Admin/Providers/RepositoryServiceProvider.php và ServiceServiceProvider.php.
-   Thêm routes admin theo nhóm prefix và tên route theo chuẩn (create/store/index/edit/update/delete), dùng middleware auth:admin và authorizeService nếu thuộc dịch vụ.
-   Phần TempSeeder chỉnh sửa lại cho phù hợp
-   Thêm cấu hình cột trong config/datatables_columns.php với key <key> (ví dụ 'delivery_goods_type') khớp DataTable, thêm config/admin_sidebar.php nếu cần.
-   Dùng các key nào thì phải bổ sung thêm vào file vi.json ví dụ như good_list: Danh sách hàng hóa chẳng hạn.
-   Tạo view blade crud các thông tin cho phù hợp tôi có trick cho bạn là copy thư mục đã chuẩn sẵn rồi thay thế content thôi đuôi thư mục ko bao giờ có s ví dụ delivery_goods_types thì phải là delivery_goods_type: admin/<snake_plural>/index|create|edit và DataTable có view partial nếu dùng.
-   Về phần api hãy cấu trúc sẵn cho tôi service và repository luôn có gì để tôi viết api

## 📁 CẤU TRÚC THƯ MỤC CHUẨN

### 1. MODEL - `app/Models/`

```
app/Models/ModelName.php                    # Model Eloquent chính
```

### 2. REPOSITORY - `app/Admin/Repositories/`

```
app/Admin/Repositories/ModelName/
├── ModelNameRepositoryInterface.php       # Interface
└── ModelNameRepository.php                # Implementation extends EloquentRepository
```

### 3. SERVICE - `app/Admin/Services/`

```
app/Admin/Services/ModelName/
├── ModelNameServiceInterface.php          # Interface
└── ModelNameService.php                   # Implementation
```

### 4. REQUEST - `app/Admin/Http/Requests/`

```
app/Admin/Http/Requests/ModelName/
└── ModelNameRequest.php                   # extends BaseRequest
```

### 5. CONTROLLER - `app/Admin/Http/Controllers/`

```
app/Admin/Http/Controllers/ModelName/
└── ModelNameController.php                # extends base Controller
```

### 6. DATATABLE - `app/Admin/DataTables/`

```
app/Admin/DataTables/ModelName/
└── ModelNameDataTable.php                 # extends BaseDataTable
```

### 7. VIEWS - `resources/views/admin/`

```
resources/views/admin/model_name/          # snake_case, không có 's'
├── index.blade.php                        # Danh sách
├── create.blade.php                       # Tạo mới
├── edit.blade.php                         # Chỉnh sửa
├── forms/
│   ├── form.blade.php                     # Form chung
│   ├── create-left.blade.php                   # Form tạo
│   ├── create-right.blade.php                   # Form tạo
│   ├── edit-left.blade.php                     # Form sửa
│   ├── edit-right.blade.php                     # Form sửa
│   └── partials/                          # Các phần nhỏ
└── datatable/
    ├── columns.blade.php                  # Cột DataTable
    └── filters.blade.php                  # Bộ lọc
```

### 8. ROUTES - `routes/admin.php`

```php
Route::prefix('/model_name')->as('model_name.')->middleware('auth:admin')->group(function () {
    Route::controller(ModelNameController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/create', 'store')->name('store');
        Route::get('/edit/{id}', 'edit')->name('edit');
        Route::put('/edit/{id}', 'update')->name('update');
        Route::delete('/delete/{id}', 'delete')->name('delete');
    });
});
```

### 9. CONFIG FILES

-   **datatables_columns.php**: Thêm key `'model_name'` với columns
-   **admin_sidebar.php**: Thêm menu với permissions
-   **vi.json**: Thêm translations cho các key sử dụng

### 10. PROVIDER BINDING

-   **RepositoryServiceProvider.php**: Binding Repository
-   **ServiceServiceProvider.php**: Binding Service

## 🔄 QUY TRÌNH TẠO CRUD

1. Model → Repository → Service → Request → Controller → DataTable → Views → Routes → Config → Provider Binding

## ⚠️ LƯU Ý QUAN TRỌNG

-   Tên class: Không có dấu hiệu số nhiều (VD: `DeliveryGoodTypeRequest` không phải `DeliveryGoodsTypeRequest`)
-   Tên thư mục view: snake_case, không có 's' (VD: `delivery_good_type` không phải `delivery_good_types`)
-   Validation exists: `exists:App\Models\ModelName,id` (không dùng `\\`)
-   Import models đầy đủ trong Request
-   Copy thư mục view chuẩn rồi thay thế content
-   Dùng cấu trúc button chuẩn: `<x-button.submit>`, `<x-button.modal-delete>`
