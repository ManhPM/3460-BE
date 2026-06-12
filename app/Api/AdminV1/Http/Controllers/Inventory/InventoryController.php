<?php

namespace App\Api\AdminV1\Http\Controllers\Inventory;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Inventory\UpdateQuantityRequest;
use App\Api\AdminV1\Http\Resources\Inventory\InventoryResource;
use App\Api\AdminV1\Http\Resources\Inventory\InventoryCollection;
use App\Api\AdminV1\Repositories\Inventory\InventoryRepositoryInterface;
use App\Api\AdminV1\Services\Inventory\InventoryService;
use Illuminate\Support\Facades\Log;

class InventoryController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        InventoryRepositoryInterface $repository,
        InventoryService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $products = $this->repository->getFiltered();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new InventoryCollection($products),
        ]);
    }

    public function getData()
    {
        return $this->index();
    }

    public function updateQuantity(UpdateQuantityRequest $request)
    {
        try {
            $validated = $request->validated();

            $admin = auth('admin')->user();
            $isSuperAdmin = $admin && $admin->hasRole('superAdmin');

            $adminId = (int) ($validated['admin_id'] ?? 0);
            if (!$isSuperAdmin) {
                $adminId = (int) $admin->id;
            }

            if ($adminId <= 0) {
                return response()->json([
                    'status' => 422,
                    'message' => __('admin_id_invalid'),
                ], 422);
            }


            $productId = isset($validated['product_id']) && $validated['product_id'] !== null ? (int) $validated['product_id'] : null;
            $productVariationId = isset($validated['product_variation_id']) && $validated['product_variation_id'] !== null ? (int) $validated['product_variation_id'] : null;
            $rawQty = $validated['qty'];
            $qty = (is_numeric($rawQty) && (float) $rawQty == (int) $rawQty) ? (int) $rawQty : 0;

            if (!$productId && !$productVariationId) {
                return response()->json([
                    'status' => 422,
                    'message' => 'Phải có product_id hoặc product_variation_id',
                ], 422);
            }

            $result = $this->service->updateQuantity($adminId, $productId, $productVariationId, $qty);

            if (!$result) {
                return response()->json([
                    'status' => 404,
                    'message' => 'Không tìm thấy sản phẩm hoặc phân loại',
                ], 404);
            }

            return response()->json([
                'status' => 200,
                'message' => __('inventory.quantity_updated_success'),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi: ' . $e->getMessage(),
            ], 500);
        }
    }
}
