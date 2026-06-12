<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\CategoryRepository;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $categories = $this->categoryRepository->getFiltered($request->all());
        return response()->json($categories);
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'required|in:active,inactive',
            'order' => 'nullable|integer',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $category = $this->categoryRepository->create($validated);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Tạo danh mục thành công'
        ], 201);
    }

    /**
     * Display the specified category
     */
    public function show(int $id)
    {
        $category = $this->categoryRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category
        ]);
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'image' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive',
            'order' => 'nullable|integer',
        ]);

        $this->categoryRepository->update($id, $validated);
        $category = $this->categoryRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $category,
            'message' => 'Cập nhật danh mục thành công'
        ]);
    }

    /**
     * Remove the specified category
     */
    public function destroy(int $id)
    {
        // Check if category has products
        if ($this->categoryRepository->hasProducts($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa danh mục có sản phẩm'
            ], 400);
        }

        $this->categoryRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa danh mục thành công'
        ]);
    }
}
