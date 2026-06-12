<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Inventory;
use App\Models\Product;

class InventoryController extends Controller
{
    /**
     * Display a listing of inventory
     */
    public function index(Request $request)
    {
        $query = Inventory::with(['product', 'branch']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('product', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by branch
        if ($request->has('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Pagination
        $perPage = $request->get('per_page', 10);
        $inventory = $query->paginate($perPage);

        return response()->json($inventory);
    }

    /**
     * Get inventory data (all items without pagination)
     */
    public function getData(Request $request)
    {
        $inventory = Inventory::with(['product', 'branch'])->get();

        return response()->json([
            'success' => true,
            'data' => $inventory
        ]);
    }

    /**
     * Update inventory quantity
     */
    public function updateQuantity(Request $request, Inventory $inventory)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $inventory->update($validated);

        return response()->json([
            'success' => true,
            'data' => $inventory->load(['product', 'branch']),
            'message' => 'Cập nhật tồn kho thành công'
        ]);
    }
}
