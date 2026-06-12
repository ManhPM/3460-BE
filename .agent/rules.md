# Datgi Project - Master Rules

## ⚠️ PHẢI ĐỌC trước khi implement bất kỳ thay đổi nào

Dự án Datgi sử dụng kiến trúc **N-Tier** (Repository-Service-Controller) với 2 layer:
- **Admin Layer** (`app/Admin/`): CMS quản trị (CRUD + DataTable + Views)
- **API Layer** (`app/Api/V1/`): RESTful API cho mobile/frontend

### Rules chi tiết được tách thành các file riêng:

| File | Nội dung |
|---|---|
| [rules/01-admin-module.md](rules/01-admin-module.md) | Cấu trúc Admin: Controller, Repository, Service, Request, DataTable |
| [rules/02-admin-views.md](rules/02-admin-views.md) | View templates, form layouts, data binding rules |
| [rules/03-api-module.md](rules/03-api-module.md) | Cấu trúc API V1: Controller, Resource, Repository, Service |
| [rules/04-config-routes.md](rules/04-config-routes.md) | Config files, Routes, Sidebar, DataTable columns |
| [rules/05-providers.md](rules/05-providers.md) | Provider registration (⛔ CRITICAL) |
| [rules/06-checklist.md](rules/06-checklist.md) | Checklist & Naming Convention khi implement module mới |
| [rules/07-coding-standards.md](rules/07-coding-standards.md) | Coding standards, pitfalls, best practices |
| [rules/08-error-handling.md](rules/08-error-handling.md) | Error handling patterns, HTTP status codes |
| [rules/09-testing.md](rules/09-testing.md) | Feature/Unit tests, chạy tests |

### Quy tắc áp dụng:
1. Khi implement module mới → đọc **TẤT CẢ** file rules
2. Khi sửa module cũ → đọc file rules liên quan
3. Khi debug lỗi injection → đọc `05-providers.md`
4. Khi thêm đường dẫn → đọc `04-config-routes.md`
