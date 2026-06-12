# Coding Standards & Best Practices

## 1. Base Classes

### EloquentRepository (`app/Admin/Repositories/EloquentRepository.php`)
- CRUD: `find()`, `findOrFail()`, `create()`, `update()`, `delete()`, `getAll()`
- Query: `getQueryBuilder()`, `getQueryBuilderOrderBy()`, `getBy()`
- Filters: `applyRequestFilters()`, `applyRequestFiltersWithDates()`
- Scopes: `scopeByAdminBusinesses()` – scope query theo admin's businesses
- Relations: `syncModelRoles()`, `assignRoles()`, `attachRelations()`, `syncRelationshipIds()`

### BaseRequest (`app/Admin/Http/Requests/BaseRequest.php`)
- Extends `FormRequest`
- Auto-dispatch validation theo HTTP method
- Override: `methodGet()`, `methodPost()`, `methodPut()`, `methodPatch()`, `methodDelete()`

## 2. Pitfalls thường gặp

| Lỗi | Nguyên nhân | Fix |
|---|---|---|
| `Target is not instantiable` | Quên đăng ký Provider | Thêm vào `RepositoryServiceProvider` / `ServiceServiceProvider` |
| `View not found` | Sai path trong `$this->view` | Kiểm tra thư mục `resources/views/admin/{module}/` |
| `Route not defined` | Quên thêm route | Thêm vào `routes/admin.php` |
| `Undefined property: stdClass::$field` | Dùng `->` cho array data | Chuyển sang `['field']` |
| `Column not found` | DataTable column config sai | Kiểm tra `config/datatables_columns.php` |

## 3. Best Practices

1. **Singleton** cho tất cả Repository/Service (đã config sẵn trong Provider)
2. **Tách validation**: `methodPost()` / `methodPut()` riêng biệt cho unique constraints
3. **Permission middleware**: Luôn kết hợp `permission:{action}` + `auth:admin`
4. **Image upload**: Dùng `<x-input-image-datgi>` cho tất cả form upload ảnh
5. **Date fields**: Dùng `flatpickr` class
6. **DataTable columns**: Config PHẢI match với key trả về từ DataTable class
7. **View data**: Truyền dạng mảng associative, tránh `compact()` khi nhiều biến
8. **API response**: Luôn wrap trong `response()->json(['status' => ..., 'message' => ..., 'data' => ...])` 
