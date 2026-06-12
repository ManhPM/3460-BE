# Module Implementation Checklist & Naming Convention

## Checklist khi implement Module mới

### Admin Layer
- [ ] `app/Admin/Repositories/{Module}/{Module}RepositoryInterface.php`
- [ ] `app/Admin/Repositories/{Module}/{Module}Repository.php`
- [ ] `app/Admin/Services/{Module}/{Module}ServiceInterface.php`
- [ ] `app/Admin/Services/{Module}/{Module}Service.php`
- [ ] `app/Admin/Http/Controllers/{Module}/{Module}Controller.php`
- [ ] `app/Admin/Http/Requests/{Module}/{Module}Request.php`
- [ ] `app/Admin/DataTables/{Module}/{Module}DataTable.php`
- [ ] `resources/views/admin/{module_snake}/index.blade.php`
- [ ] `resources/views/admin/{module_snake}/create.blade.php`
- [ ] `resources/views/admin/{module_snake}/edit.blade.php`
- [ ] `resources/views/admin/{module_snake}/forms/create-left.blade.php`
- [ ] `resources/views/admin/{module_snake}/forms/create-right.blade.php`
- [ ] `resources/views/admin/{module_snake}/forms/edit-left.blade.php`
- [ ] `resources/views/admin/{module_snake}/forms/edit-right.blade.php`
- [ ] `resources/views/admin/{module_snake}/datatable/action.blade.php`

### Config & Routes
- [ ] `config/datatables_columns.php` → thêm column config
- [ ] `config/admin_sidebar.php` → thêm menu item
- [ ] `routes/admin.php` → thêm route group

### Providers (⛔ SẼ LỖI NẾU QUÊN)
- [ ] `app/Admin/Providers/RepositoryServiceProvider.php`
- [ ] `app/Admin/Providers/ServiceServiceProvider.php`

### API Layer (nếu cần)
- [ ] `app/Api/V1/Http/Controllers/{Module}/{Module}Controller.php`
- [ ] `app/Api/V1/Http/Requests/{Module}/` (request files)
- [ ] `app/Api/V1/Http/Resources/{Module}/{Module}Resource.php`
- [ ] `app/Api/V1/Repositories/{Module}/{Module}RepositoryInterface.php`
- [ ] `app/Api/V1/Repositories/{Module}/{Module}Repository.php`
- [ ] `app/Api/V1/Services/{Module}/{Module}ServiceInterface.php`
- [ ] `app/Api/V1/Services/{Module}/{Module}Service.php`
- [ ] `app/Api/V1/Providers/RepositoryServiceProvider.php`
- [ ] `app/Api/V1/Providers/ServiceServiceProvider.php`
- [ ] `routes/api.php` → thêm routes

### Hệ thống
- [ ] Permissions: `create{Module}`, `view{Module}`, `update{Module}`, `delete{Module}`
- [ ] Model: `app/Models/{Module}.php`
- [ ] Migration (nếu cần): `database/migrations/`

---

## Naming Convention

| Thành phần | Convention | Ví dụ |
|---|---|---|
| Module folder | PascalCase | `Customer`, `OrderReport` |
| Database table | snake_case (số nhiều) | `customers`, `order_reports` |
| Model | PascalCase (số ít) | `Customer`, `OrderReport` |
| Route URL prefix | kebab-case | `/customers`, `/order-reports` |
| Route alias | snake_case | `customer.`, `order_report.` |
| View folder | snake_case | `admin/customer/`, `admin/order_report/` |
| Config key | snake_case | `'customer'`, `'order_report'` |
| Permission | camelCase | `createCustomer`, `viewOrderReport` |
| Sidebar title | snake_case + `_management` | `customer_management` |
