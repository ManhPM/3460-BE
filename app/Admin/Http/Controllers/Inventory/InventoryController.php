<?php

namespace App\Admin\Http\Controllers\Inventory;

use App\Admin\Http\Controllers\BaseController;
use App\Models\Admin;
use App\Models\AdminInventory;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InventoryController extends BaseController
{
    public function index(Request $request): Factory|View|Application
    {
        $admin = auth('admin')->user();
        $selectedAdminId = (int) $request->get('admin_id', 2);

        $isSuperAdmin = $admin && $admin->hasRole('superAdmin');

        if (!$isSuperAdmin) {
            $selectedAdminId = (int) $admin->id;
        }

        $admins = [];
        if ($isSuperAdmin) {
            $admins = Admin::query()
                ->select(['id', 'branch_name', 'branch_phone', 'branch_address'])
                ->whereHas('roles', function ($q) {
                    $q->where('name', 'branch');
                })
                ->get();

            // SuperAdmin tổng (id=1): tự chọn chi nhánh đầu tiên nếu chưa chọn cụ thể
            if ($admin->id === 1 && $selectedAdminId === 0 && $admins->isNotEmpty()) {
                $selectedAdminId = (int) $admins->first()->id;
            }
        }

        return view('admin.inventories.index', [
            'isSuperAdmin' => $isSuperAdmin,
            'admins' => $admins,
            'selectedAdminId' => $selectedAdminId,
            'breadcrumbs' => $this->crums->add(__('Quản lý tồn chi nhánh'))
        ]);
    }

    public function data(Request $request): JsonResponse
    {
        $admin = auth('admin')->user();
        $isSuperAdmin = $admin && $admin->hasRole('superAdmin');

        $adminId = (int) $request->get('admin_id');
        if (!$isSuperAdmin) {
            $adminId = (int) $admin->id;
        }
        if ($adminId <= 0) {
            return response()->json(['html' => '', 'count' => 0]);
        }

        $search = trim((string) $request->get('q', ''));

        $productsQuery = Product::query()
            ->select(['id', 'name', 'avatar', 'price', 'promotion_price', 'type'])
            ->with(['productVariations' => function ($q) {
                $q->select(['id', 'product_id', 'price', 'promotion_price', 'image']);
                $q->with(['attributeVariations' => function ($q2) {
                    $q2->select(['attributes_variations.id', 'attributes_variations.name']);
                }]);
            }]);

        if ($search !== '') {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $products = $productsQuery->orderBy('id', 'desc')->paginate(20);

        // Preload admin inventory rows to avoid N+1
        $inventoryRows = AdminInventory::query()
            ->where('admin_id', $adminId)
            ->whereIn('product_id', $products->pluck('id')->all())
            ->get()
            ->groupBy(function ($row) {
                return ($row->product_id ?: 0) . ':' . ($row->product_variation_id ?: 0);
            });

        $html = view('admin.inventories.partials.table-rows', [
            'products' => $products,
            'adminId' => $adminId,
            'inventoryRows' => $inventoryRows,
            'isSuperAdmin' => $isSuperAdmin
        ])->render();

        return response()->json([
            'html' => $html,
            'count' => $products->total(),
            'has_more' => $products->hasMorePages(),
            'next_page' => $products->currentPage() + 1
        ]);
    }

    public function updateQty(Request $request): JsonResponse
    {
        $admin = auth('admin')->user();
        $isSuperAdmin = $admin && $admin->hasRole('superAdmin');

        $validated = $request->validate([
            'admin_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'product_variation_id' => ['nullable', 'integer'],
            'qty' => ['required']
        ]);

        $targetAdminId = (int) ($validated['admin_id'] ?? 0);
        if (!$isSuperAdmin) {
            $targetAdminId = (int) $admin->id;
        }
        if ($targetAdminId <= 0) {
            return response()->json(['message' => 'admin_id invalid'], 422);
        }

        $productId = isset($validated['product_id']) ? (int) $validated['product_id'] : null;
        $productVariationId = isset($validated['product_variation_id']) ? (int) $validated['product_variation_id'] : null;
        $rawQty = $validated['qty'];
        $qty = (is_numeric($rawQty) && (float) $rawQty == (int) $rawQty) ? (int) $rawQty : 0;

        if (!$productId && !$productVariationId) {
            return response()->json(['message' => 'Target required'], 422);
        }

        if ($productVariationId) {
            $variationExists = ProductVariation::query()->where('id', $productVariationId)->exists();
            if (!$variationExists) return response()->json(['message' => 'Variation not found'], 404);
            $productId = (int) ProductVariation::query()->where('id', $productVariationId)->value('product_id');
        } else {
            $productExists = Product::query()->where('id', $productId)->exists();
            if (!$productExists) return response()->json(['message' => 'Product not found'], 404);
        }

        try {
            DB::transaction(function () use ($targetAdminId, $productId, $productVariationId, $qty) {
                AdminInventory::query()->updateOrCreate(
                    [
                        'admin_id' => $targetAdminId,
                        'product_id' => $productId,
                        'product_variation_id' => $productVariationId,
                    ],
                    [
                        'qty' => $qty,
                    ]
                );
            });
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Update failed'], 500);
        }

        return response()->json(['message' => 'OK']);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // Import tồn kho từ file Excel upload
    // ─────────────────────────────────────────────────────────────────────────
    public function importFromExcel(Request $request): JsonResponse
    {
        $admin       = auth('admin')->user();
        $isSuperAdmin = $admin && $admin->hasRole('superAdmin');

        $request->validate([
            'file'     => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
            'admin_id' => ['nullable', 'integer'],
            'mode'     => ['nullable', 'in:set,add'],
        ]);

        $targetAdminId = (int) $request->input('admin_id', 2);
        if (!$isSuperAdmin) {
            $targetAdminId = (int) $admin->id;
        }
        if ($targetAdminId <= 0) {
            return response()->json(['message' => 'Vui lòng chọn chi nhánh.'], 422);
        }

        $mode = $request->input('mode', 'set'); // 'set' = ghi đè | 'add' = cộng dồn

        // ── Đọc Excel ─────────────────────────────────────────────────────────
        try {
            $importObject = new class implements \Maatwebsite\Excel\Concerns\WithCalculatedFormulas {};
            $data = Excel::toArray($importObject, $request->file('file'));
        } catch (\Throwable $e) {
            return response()->json(['message' => 'Không thể đọc file Excel: ' . $e->getMessage()], 422);
        }

        $rows = $data[0] ?? [];
        array_shift($rows); // bỏ header

        if (empty($rows)) {
            return response()->json(['message' => 'File không có dữ liệu.'], 422);
        }

        $updated    = 0;
        $created    = 0;
        $skipped    = 0;
        $notFound   = 0;
        $notFoundList = [];

        DB::beginTransaction();
        try {
            foreach ($rows as $index => $row) {
                // col4 = Tên hàng, col5 = Tồn kho, col6 = ĐVT
                $productName = trim($row[4] ?? '');
                $rawQty      = $row[5] ?? 0;
                $qty         = (is_numeric($rawQty) && (float) $rawQty == (int) $rawQty) ? (int) $rawQty : 0;
                $unitRaw     = isset($row[6]) ? trim($row[6]) : null;

                if (empty($productName)) {
                    $skipped++;
                    continue;
                }

                $unit = (!empty($unitRaw)) ? ucfirst(mb_strtolower($unitRaw)) : null;

                // Tìm product theo tên
                $product = Product::whereRaw('LOWER(TRIM(name)) = ?', [mb_strtolower(trim($productName))])->first();

                if (!$product) {
                    $notFound++;
                    $notFoundList[] = 'Dòng ' . ($index + 2) . ': "' . $productName . '" — Không tìm thấy sản phẩm';
                    continue;
                }

                $productVariationId = null;

                if (!empty($unit)) {
                    $variation = $product->productVariations()
                        ->whereHas('attributeVariations', function ($q) use ($unit) {
                            $q->whereRaw('LOWER(TRIM(attributes_variations.name)) = ?', [mb_strtolower(trim($unit))]);
                        })
                        ->first();

                    if (!$variation) {
                        $notFound++;
                        $notFoundList[] = 'Dòng ' . ($index + 2) . ': "' . $productName . '" — Không tìm thấy biến thể "' . $unit . '"';
                        continue;
                    }

                    $productVariationId = $variation->id;
                }

                $conditions = [
                    'admin_id'             => $targetAdminId,
                    'product_id'           => $product->id,
                    'product_variation_id' => $productVariationId,
                ];

                $inventory = AdminInventory::where($conditions)->first();

                if ($inventory) {
                    $newQty = ($mode === 'add') ? ($inventory->qty + $qty) : $qty;
                    $inventory->update(['qty' => $newQty]);
                    $updated++;
                } else {
                    AdminInventory::create(array_merge($conditions, ['qty' => $qty]));
                    $created++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Lỗi khi xử lý: ' . $e->getMessage()], 500);
        }

        return response()->json([
            'message'   => 'Cập nhật tồn kho hoàn tất.',
            'updated'   => $updated,
            'created'   => $created,
            'skipped'   => $skipped,
            'not_found' => $notFound,
            'not_found_list' => $notFoundList,
        ]);
    }
}
