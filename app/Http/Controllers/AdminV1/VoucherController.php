<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Voucher;
use Illuminate\Support\Str;

class VoucherController extends Controller
{
    /**
     * Display a listing of vouchers
     */
    public function index(Request $request)
    {
        $query = Voucher::query();

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
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
        $vouchers = $query->paginate($perPage);

        return response()->json($vouchers);
    }

    /**
     * Store a newly created voucher
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|unique:vouchers,code|max:50',
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        // Auto-generate code if not provided
        if (empty($validated['code'])) {
            $validated['code'] = 'VC-' . strtoupper(Str::random(8));
        }

        $voucher = Voucher::create($validated);

        return response()->json([
            'success' => true,
            'data' => $voucher,
            'message' => 'Tạo mã giảm giá thành công'
        ], 201);
    }

    /**
     * Display the specified voucher
     */
    public function show(Voucher $voucher)
    {
        return response()->json([
            'success' => true,
            'data' => $voucher
        ]);
    }

    /**
     * Update the specified voucher
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code' => 'sometimes|required|string|unique:vouchers,code,' . $voucher->id . '|max:50',
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:percentage,fixed',
            'value' => 'sometimes|required|numeric|min:0',
            'min_order_amount' => 'nullable|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'usage_per_user' => 'nullable|integer|min:1',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after:start_date',
            'status' => 'sometimes|required|in:active,inactive',
            'description' => 'nullable|string',
        ]);

        $voucher->update($validated);

        return response()->json([
            'success' => true,
            'data' => $voucher,
            'message' => 'Cập nhật mã giảm giá thành công'
        ]);
    }

    /**
     * Remove the specified voucher
     */
    public function destroy(Voucher $voucher)
    {
        $voucher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa mã giảm giá thành công'
        ]);
    }

    /**
     * Toggle voucher status
     */
    public function toggleStatus(Voucher $voucher)
    {
        $voucher->update([
            'status' => $voucher->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'data' => $voucher,
            'message' => 'Cập nhật trạng thái thành công'
        ]);
    }
}
