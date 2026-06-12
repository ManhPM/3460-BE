## 🎨 FRONTEND (Next.js/React)

### 1. **Types** (`src/features/{module}/types/{module}.ts`)

**File cấu trúc:**

```
src/features/{module}/types/
└── {module}.ts
```

**Content:**

- [ ] Interface chính (VD: `Bank`, `MembershipLevel`)
- [ ] `Filters` interface (nếu cần filter phức tạp)
- [ ] `CreateInput` interface
- [ ] `UpdateInput` interface (có `id: number` nếu có update)
- [ ] Export types đầy đủ
- [ ] **Lưu ý**: User interface có `name` (từ API) nhưng backend dùng `fullname`

**Ví dụ:**

```typescript
export interface Bank {
    id: number;
    bank_id: string;
    bank_account: string;
    bank_account_number: string;
    created_at?: string;
    updated_at?: string;
}

export interface CreateBankInput {
    bank_id: string;
    bank_account: string;
    bank_account_number: string;
}

export interface UpdateBankInput extends CreateBankInput {
    id: number;
}
```

### 2. **API Hooks** (`src/features/{module}/api/use{Module}.ts`)

**File cấu trúc:**

```
src/features/{module}/api/
└── use{Module}.ts
```

**Exports cần có:**

- [ ] `use{Module}s(filters?)` - List với pagination

    - Return: `{ data?: { data: Type[]; meta: PaginationMeta; links?: Links } }`
    - Query key: `['{module}', filters]`

    ```typescript
    export const use{Module}s = (filters?: Record<string, any>) => {
        const queryFn = async () => {
            const response = await apiClient.get<{
                data: Type[];
                meta: { current_page: number; last_page: number; per_page: number; total: number };
                links?: any;
            }>(API_ENDPOINTS.{MODULE}.LIST, { params: filters });
            return response.data;
        };
        return useQuery({
            queryKey: ['{module}', filters],
            queryFn,
            enabled: true,
        });
    };
    ```

- [ ] `use{Module}(id)` - Chi tiết (optional nếu không cần)
- [ ] `useCreate{Module}()` - Tạo mới

    - Mutation: `mutationFn: async (data: CreateInput) => ...`
    - Query invalidation: `queryClient.invalidateQueries({ queryKey: ['{module}'] })`
    - Toast: success/error messages

- [ ] `useUpdate{Module}()` - Cập nhật (nếu cần)
    ```typescript
    export const useUpdate{Module} = () => {
        const queryClient = useQueryClient();
        return useMutation({
            mutationFn: async ({ id, data }: { id: number; data: UpdateInput }) => {
                const response = await apiClient.put<{ data: Type }>(
                    API_ENDPOINTS.{MODULE}.UPDATE(id),
                    { ...data, id }, // Backend yêu cầu id trong body
                );
                return response.data.data;
            },
            onSuccess: () => {
                queryClient.invalidateQueries({ queryKey: ['{module}'] });
                toast.success('Updated successfully');
            },
            onError: () => {
                toast.error('Failed to update');
            },
        });
    };
    ```
- [ ] `useDelete{Module}()` - Xóa
- [ ] `useUnique{Module}s(searchTerm)` - Search cho select (nếu cần relationship)
    - Query key: `['{module}-unique', searchTerm]`
    - Filter servers-side qua params

### 3. **Columns Config** (`src/features/{module}/config/columns.tsx`)

**File cấu trúc:**

```
src/features/{module}/config/
└── columns.tsx
```

**Interfaces & Enums:**

```typescript
export enum SearchType {
    TEXT = "text",
    SELECT = "select",
    DATE = "date",
    DATETIME = "datetime",
}

export interface ColumnSearchConfig {
    type: SearchType;
    label: string;
    placeholder?: string;
    options?: Array<{ value: string | number; label: string }>;
}

export interface ColumnConfig {
    key: string; // Match database field name
    header: string;
    search?: ColumnSearchConfig;
    cell?: (row: Type) => React.ReactNode;
    sortable?: boolean;
}
```

**Exports:**

- [ ] `{module}ColumnsConfig: ColumnConfig[]` - Array configs
    ```typescript
    export const banksColumnsConfig: ColumnConfig[] = [
        {
            key: "id",
            header: "ID",
            search: { type: SearchType.TEXT, label: "Search ID", placeholder: "Enter ID" },
        },
        {
            key: "bank_id",
            header: "Bank ID",
            search: { type: SearchType.TEXT, label: "Search Bank ID" },
        },
        // ...
    ];
    ```
- [ ] `generateColumnsFromConfig()` - Generate columns với action buttons (Edit, Delete)
    - Add action column cuối cùng
    - Mỗi action là button icon
- [ ] `generateSearchColumnsFromConfig()` - Generate search columns từ config

### 4. **Page Component** (`src/app/(dashboard)/dashboard/{module}/page.tsx`)

**File cấu trúc:**

```
src/app/(dashboard)/dashboard/{module}/
└── page.tsx
```

**Imports cần có:**

```typescript
'use client';
import { useState, useRef } from 'react';
import { motion } from 'framer-motion';
import { useTranslation } from '@/lib/i18n';
import { toast } from 'sonner';
import { Plus, Loader2 } from 'lucide-react';
import { PageHeader } from '@/components/shared/PageHeader';
import { AdvancedDataTable } from '@/components/data-table/AdvancedDataTable';
import ConfirmDialog from '@/components/shared/ConfirmDialog';
import { Dialog, DialogContent, DialogHeader, DialogTitle, DialogDescription } from '@/components/ui/dialog';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { use{Module}s, useCreate{Module}, useUpdate{Module}, useDelete{Module} } from '@/features/{module}/api/use{Module}';
import { {module}ColumnsConfig, generateColumnsFromConfig, generateSearchColumnsFromConfig } from '@/features/{module}/config/columns';
```

**State Management:**

```typescript
const [page, setPage] = useState(1);
const [filters, setFilters] = useState<Record<string, any>>({});
const [deleteId, setDeleteId] = useState<number | null>(null);
const [isEditDialogOpen, setIsEditDialogOpen] = useState(false);
const [editingItem, setEditingItem] = useState<Type | null>(null);
const [formData, setFormData] = useState<CreateInput>({
    /* init values */
});
const [isLoading, setIsLoading] = useState(false);
// Optional: search terms cho select
const [searchTerm, setSearchTerm] = useState("");
const inputRef = useRef<HTMLInputElement>(null);
```

**Hooks:**

```typescript
const { t } = useTranslation();
const { data, isLoading: isDataLoading } = use{Module}s(filters);
const createMutation = useCreate{Module}();
const updateMutation = useUpdate{Module}();
const deleteMutation = useDelete{Module}();
```

**Handler Functions:**

```typescript
const handleAdd = () => {
    setEditingItem(null);
    setFormData({
        /* reset to initial */
    });
    setIsEditDialogOpen(true);
};

const handleEdit = (item: Type) => {
    setEditingItem(item);
    setFormData({
        /* populate from item */
    });
    setIsEditDialogOpen(true);
};

const handleSave = async () => {
    // Validation
    if (!formData.field1 || !formData.field2) {
        toast.error("Please fill all required fields");
        return;
    }

    setIsLoading(true);
    try {
        if (editingItem) {
            await updateMutation.mutateAsync({
                id: editingItem.id,
                data: formData,
            });
        } else {
            await createMutation.mutateAsync(formData);
        }
        setIsEditDialogOpen(false);
        setFormData({
            /* reset */
        });
    } finally {
        setIsLoading(false);
    }
};

const handleDelete = (id: number) => {
    setDeleteId(id);
};

const confirmDelete = async () => {
    if (!deleteId) return;
    try {
        await deleteMutation.mutateAsync(deleteId);
        setDeleteId(null);
    } catch (error) {
        console.error("Delete error:", error);
    }
};

const handleCancel = () => {
    setIsEditDialogOpen(false);
    setFormData({
        /* reset */
    });
};
```

**UI Structure:**

```typescript
return (
    <div className="space-y-4">
        {/* Page Header */}
        <PageHeader
            title="Manage {Module}"
            description="Add, edit, or delete {module}"
            action={
                <Button onClick={handleAdd} className="gap-2">
                    <Plus className="h-4 w-4" />
                    Add New
                </Button>
            }
        />

        {/* Data Table */}
        <AdvancedDataTable
            columns={columns}
            data={data?.data || []}
            isLoading={isDataLoading}
            pageCount={data?.meta?.last_page || 1}
            pageIndex={page - 1}
            pageSize={10}
            onPaginationChange={(newPage) => setPage(newPage + 1)}
            onSearchChange={(searchParams) => {
                setFilters(searchParams);
                setPage(1);
            }}
            globalSearch={false}
            searchColumns={searchColumns}
        />

        {/* Delete Confirm Dialog */}
        <ConfirmDialog
            isOpen={deleteId !== null}
            title="Confirm Delete"
            description="Are you sure you want to delete this item?"
            onConfirm={confirmDelete}
            onCancel={() => setDeleteId(null)}
            variant="destructive"
            isLoading={deleteMutation.isPending}
        />

        {/* Form Dialog */}
        <Dialog open={isEditDialogOpen} onOpenChange={setIsEditDialogOpen}>
            <DialogContent>
                <DialogHeader className="relative">
                    <div className="flex items-start justify-between">
                        <div className="flex-1">
                            <DialogTitle>
                                {editingItem ? 'Edit {Module}' : 'Add New {Module}'}
                            </DialogTitle>
                            <DialogDescription>
                                Fill in the form below to {editingItem ? 'update' : 'create'} a {module}
                            </DialogDescription>
                        </div>
                        <div className="flex items-center gap-2 ml-4">
                            <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                <Button
                                    variant="outline"
                                    onClick={handleCancel}
                                    className="border-red-500 text-red-600 hover:text-red-700 dark:border-red-400 dark:text-red-400 dark:hover:text-red-300 cursor-pointer"
                                >
                                    Cancel
                                </Button>
                            </motion.div>
                            <motion.div whileHover={{ scale: 1.02 }} whileTap={{ scale: 0.98 }}>
                                <Button
                                    variant="outline"
                                    onClick={handleSave}
                                    disabled={isLoading}
                                    className={`gap-2 ${editingItem ? 'border-blue-500 text-blue-600 hover:text-blue-700 dark:border-blue-400 dark:text-blue-400 dark:hover:text-blue-300' : 'border-green-500 text-green-600 hover:text-green-700 dark:border-green-400 dark:text-green-400 dark:hover:text-green-300'} cursor-pointer`}
                                >
                                    {isLoading && <Loader2 className="h-4 w-4 animate-spin" />}
                                    {editingItem ? 'Update' : 'Add New'}
                                </Button>
                            </motion.div>
                        </div>
                    </div>
                </DialogHeader>

                <div className="space-y-4 py-4">
                    {/* Form Fields */}
                    <div>
                        <Label htmlFor="field1">Field 1</Label>
                        <Input
                            id="field1"
                            value={formData.field1}
                            onChange={(e) => setFormData({ ...formData, field1: e.target.value })}
                            maxLength={255}
                        />
                    </div>
                    {/* ... more fields ... */}
                </div>
            </DialogContent>
        </Dialog>
    </div>
);
```

**Quan trọng:**

- [ ] **Không dùng `DialogFooter`** - Đặt buttons ở `DialogHeader` góc phải
- [ ] **Wrap buttons** trong `motion.div` với animations
- [ ] **Button styling**: Outline variant với màu theo action (Cancel: red, Add: green, Update: blue)
- [ ] **globalSearch={false}** - Chỉ dùng column-specific search
- [ ] **Invalidate queries** sau CRUD operations
- [ ] Toast messages cho success/error

### 5. **API Endpoints** (`src/lib/api/endpoints.ts`)

**Location:** `src/lib/api/endpoints.ts` - Tập trung tất cả endpoints

**Pattern:**

```typescript
export const API_ENDPOINTS = {
    // ...existing endpoints...

    {MODULE_NAME}: {
        LIST: '/{module-name}',
        DETAIL: (id: number) => `/{module-name}/${id}`,
        CREATE: '/{module-name}',
        UPDATE: (id: number) => `/{module-name}/${id}`,
        DELETE: (id: number) => `/{module-name}/${id}`,
        // Optional additional endpoints
        SEARCH: '/{module-name}/search',
        BULK_DELETE: '/{module-name}/bulk-delete',
    },
};
```

**Ví dụ:**

```typescript
BANKS: {
    LIST: '/banks',
    DETAIL: (id: number) => `/banks/${id}`,
    CREATE: '/banks',
    UPDATE: (id: number) => `/banks/${id}`,
    DELETE: (id: number) => `/banks/${id}`,
},
```

---

## 📁 FOLDER STRUCTURE CHEATSHEET

```
Frontend (src/features/{module}/)
├── api/
│   └── use{Module}.ts          (All API hooks: list, detail, create, update, delete)
├── config/
│   └── columns.tsx             (Column config, generateColumns functions)
├── types/
│   └── {module}.ts             (TypeScript interfaces)
└── (no other folders needed for basic CRUD)

Page Component (src/app/(dashboard)/dashboard/{module}/)
└── page.tsx                    (Main page with DataTable + Dialog)

Endpoints (src/lib/api/)
└── endpoints.ts                (Centralized API endpoints)
```

---

## 🔍 CÁC ĐIỂM QUAN TRỌNG CẦN KIỂM TRA

### Backend:

1. ✅ **Repository search**: Mỗi column trong columns config phải có filter tương ứng
2. ✅ **User field**: Luôn dùng `fullname` trong database query, không phải `name`
3. ✅ **Update method**: Phải có `id` trong body data khi update
4. ✅ **Collection format**: Phải return `{ data: [], meta: {}, links: {} }`
5. ✅ **Resource relationship**: Dùng `whenLoaded()` và return `fullname` với key `name`

### Frontend:

1. ✅ **API response**: Phải return `response.data.data` (inner data object)
2. ✅ **Columns config**: Key phải match với database field hoặc request param
3. ✅ **AdvancedDataTable**: Dùng `globalSearch={false}` và `searchColumns`
4. ✅ **Form validation**: Kiểm tra required fields trước khi submit
5. ✅ **User display**: Dùng `user.name` (từ API) nhưng search dùng `fullname`
6. ✅ **Input focus**: Không có `overflow-x-hidden` để tránh cắt border
7. ✅ **Button styling**: Outlined với màu đúng, không có hover background
8. ✅ **Button position**: Nút thao tác đặt ở `DialogHeader` (góc phải), không dùng `DialogFooter`
9. ✅ **Button animations**: Wrap buttons trong `motion.div` với `whileHover` và `whileTap`
10. ✅ **Permission display**: Hiển thị `permission.title || permission.name` (nếu có module thì group theo module)

### Common Issues:

- ❌ **Global search**: Không dùng, chỉ column-specific filters
- ❌ **User name vs fullname**: Backend query dùng `fullname`, API trả về key `name`
- ❌ **Update id**: Phải có `id` trong body data
- ❌ **Collection structure**: Phải có `data`, `meta`, `links`
- ❌ **Input border**: Không dùng `overflow-x-hidden` trong DialogContent
- ❌ **DialogFooter**: Không dùng `DialogFooter` cho nút thao tác, đặt ở `DialogHeader` thay thế
- ❌ **Permission name**: Không chỉ hiển thị `permission.name`, phải dùng `permission.title || permission.name`

---

## 📝 TEMPLATE REQUEST

Khi request tạo trang mới, cung cấp:

```
Tạo trang CRUD cho [Module Name]

Backend:
- Model: [Model name]
- Fields: [List fields và types]
- Relationships: [List relationships nếu có]
- Search fields: [List fields cần search]

Frontend:
- Columns hiển thị: [List columns]
- Form fields: [List form fields]
- Validation rules: [List rules]
- Special requirements: [Any special UI/UX]
```

---

## ✅ CHECKLIST HOÀN THÀNH

Sau khi implement, kiểm tra:

- [ ] Backend: Controller, Request, Repository, Service, Resource, Collection, Routes
- [ ] Frontend: Types, API Hooks, Columns Config, Page Component, Endpoints
- [ ] Search: Tất cả columns có search đều hoạt động
- [ ] CRUD: Create, Read, Update, Delete đều hoạt động
- [ ] UI/UX: Buttons, Forms, Validation, Error handling
- [ ] Performance: Pagination, Debounce search, Memoization

---

**Lưu ý cuối**: Luôn học theo pattern của **MembershipLevels** để đảm bảo consistency!
