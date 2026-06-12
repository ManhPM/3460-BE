<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Review;

class ReviewController extends Controller
{
    /**
     * Display a listing of reviews
     */
    public function index(Request $request)
    {
        $query = Review::with(['product', 'user']);

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('comment', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q2) use ($search) {
                        $q2->where('name', 'like', "%{$search}%");
                    });
            });
        }

        // Filter by product
        if ($request->has('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        // Filter by rating
        if ($request->has('rating')) {
            $query->where('rating', $request->rating);
        }

        // Filter by status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $perPage = $request->get('per_page', 10);
        $reviews = $query->paginate($perPage);

        return response()->json($reviews);
    }

    /**
     * Display the specified review
     */
    public function show(Review $review)
    {
        return response()->json([
            'success' => true,
            'data' => $review->load(['product', 'user'])
        ]);
    }

    /**
     * Remove the specified review
     */
    public function destroy(Review $review)
    {
        $review->delete();

        return response()->json([
            'success' => true,
            'message' => 'Xóa đánh giá thành công'
        ]);
    }

    /**
     * Reply to review
     */
    public function reply(Request $request, Review $review)
    {
        $validated = $request->validate([
            'reply' => 'required|string',
        ]);

        $review->update($validated);

        return response()->json([
            'success' => true,
            'data' => $review->load(['product', 'user']),
            'message' => 'Trả lời đánh giá thành công'
        ]);
    }

    /**
     * Approve review
     */
    public function approve(Review $review)
    {
        $review->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'data' => $review->load(['product', 'user']),
            'message' => 'Duyệt đánh giá thành công'
        ]);
    }

    /**
     * Reject review
     */
    public function reject(Review $review)
    {
        $review->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'data' => $review->load(['product', 'user']),
            'message' => 'Từ chối đánh giá thành công'
        ]);
    }
}
