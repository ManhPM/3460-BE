<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Discount;

class DiscountController extends Controller
{
    /**
     * Display a listing of discounts
     */
    public function index(Request $request)
    {
        $query = Discount::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter by type
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $discounts = $query->paginate($perPage);

        return response()->json($discounts);
    }

    /**
     * Store a newly created discount
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $discount = Discount::create($validated);

        return response()->json([
            'success' => true,
            'data' => $discount,
            'message' => 'Tạo chương trình giảm giá thành công'
        ], 201);
    }

    /**
     * Display the specified discount
     */
    public function show(Discount $discount)
    {
        return response()->json([
            'success' => true,
            'data' => $discount
        ]);
    }

    /**
     * Update the specified discount
     */
    public function update(Request $request, Discount $discount)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:percentage,fixed',
            'value' => 'sometimes|required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $discount->update($validated);

        return response()->json([
            'success' => true,
            'data' => $discount,
            'message' => 'Cập nhật chương trình giảm giá thành công'
        ]);
    }

    /**
     * Remove the specified discount
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa chương trình giảm giá thành công'
        ]);
    }

    /**
     * Toggle discount status
     */
    public function toggleStatus(Discount $discount)
    {
        $discount->update([
            'status' => $discount->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'data' => $discount,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }
}
