# Admin Views Rules

## Cấu trúc thư mục View

```
resources/views/admin/{module_snake}/
├── index.blade.php              # Danh sách (DataTable)
├── create.blade.php             # Wrapper tạo mới
├── edit.blade.php               # Wrapper chỉnh sửa
├── forms/
│   ├── create-left.blade.php    # Form tạo - cột trái (col-9)
│   ├── create-right.blade.php   # Form tạo - cột phải (col-3)
│   ├── edit-left.blade.php      # Form sửa - cột trái (col-9)
│   └── edit-right.blade.php     # Form sửa - cột phải (col-3)
└── datatable/
    ├── action.blade.php         # Cột action (edit/delete)
    └── {custom_column}.blade.php
```

## ⛔ QUY TẮC DATA BINDING (CRITICAL)

### Truyền data từ Controller sang View: dùng mảng `['key']` 

```php
// ✅ ĐÚNG
return view($this->view['create'], [
    'customer_gender' => $customerGender,
    'allBusinesses'   => $allBusinesses,
]);

// ❌ SAI - KHÔNG dùng compact() khi có nhiều biến
return view('admin.customer.create', compact('customer'));
```

### Truy cập data trong Blade:
- **Biến truyền từ Controller:** dùng trực tiếp `$variable_name`
- **Eloquent Model attributes:** dùng `$model->attribute` (ĐÂY LÀ BÌNH THƯỜNG)
- **Datatable custom columns:** dùng `$row['field']` (mảng syntax)

## View Templates

### index.blade.php
```blade
@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body pt-0">
        <div class="container-xl">
            @if (auth('admin')->user()->hasPermissionTo('create{Module}'))
                <div class="d-flex justify-content-end mb-3">
                    <x-link :href="route('admin.{module_snake}.create')" class="btn btn-default-cms">
                        <i class="ti ti-plus me-1"></i>{{ __('add') }}
                    </x-link>
                </div>
            @endif
            <div class="table-responsive position-relative">
                <x-admin.partials.toggle-column-datatable />
                {{ $dataTable->table(['class' => 'table table-bordered bg-white shadow-sm', 'style' => 'min-width: 900px;'], true) }}
            </div>
        </div>
    </div>
@endsection
@push('libs-js')
    <script src="{{ asset('/public/vendor/datatables/buttons.server-side.js') }}"></script>
@endpush
@push('custom-js')
    {{ $dataTable->scripts() }}
    @include('admin.scripts.datatable-toggle-columns', ['id_table' => $dataTable->getTableAttribute('id')])
@endpush
```

### create.blade.php
```blade
@extends('admin.layouts.master')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.{module_snake}.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.{module_snake}.forms.create-left')
                    @include('admin.{module_snake}.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
```

### edit.blade.php (⚠️ phải có hidden input `id`)
```blade
@extends('admin.layouts.master')
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.{module_snake}.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="${module_snake}->id" />
                <div class="row justify-content-center">
                    @include('admin.{module_snake}.forms.edit-left')
                    @include('admin.{module_snake}.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
```

### forms/create-right.blade.php (Action sidebar)
```blade
<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header"><i class="ti ti-playstation-circle me-1"></i>{{ __('action') }}</div>
        <div class="card-body p-2">
            <x-button.submit :title="__('add')" />
        </div>
    </div>
    <!-- Avatar, Status, Business selector... -->
</div>
```

## Blade Components hay dùng

| Component | Mục đích |
|---|---|
| `<x-input>` | Text input |
| `<x-select>` + `<x-select-option>` | Dropdown |
| `<x-input-image-datgi>` | Upload ảnh |
| `<x-input-checkbox>` | Checkbox |
| `<x-form>` | Form wrapper (type: post/put) |
| `<x-button.submit>` | Nút submit |
| `<x-button.modal-delete>` | Nút xóa (modal confirm) |
| `<x-link>` | Link button |
| Flatpickr class | Date picker (`class="flatpickr"`) |
