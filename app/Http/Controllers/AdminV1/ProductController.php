<?php

namespace App\Http\Controllers\AdminV1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\ProductRepository;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * Display a listing of products
     */
    public function index(Request $request)
    {
        $products = $this->productRepository->getFiltered($request->all());
        return response()->json($products);
    }

    /**
     * Store a newly created product
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug',
            'sku' => 'required|string|unique:products,sku',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'status' => 'required|in:active,inactive,draft',
            'is_featured' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $product = $this->productRepository->create($validated);

        return response()->json([
            'success' => true,
            'data' => $product->load('category'),
            'message' => 'Tạo sản phẩm thành công'
        ], 201);
    }

    /**
     * Display the specified product
     */
    public function show(int $id)
    {
        $product = $this->productRepository->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $product->load('category')
        ]);
    }

    /**
     * Update the specified product
     */
    public function update(Request $request, int $id)
    {
        $product = $this->productRepository->findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'nullable|string|unique:products,slug,' . $id,
            'sku' => 'sometimes|required|string|unique:products,sku,' . $id,
            'price' => 'sometimes|required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'category_id' => 'sometimes|required|exists:categories,id',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string',
            'status' => 'sometimes|required|in:active,inactive,draft',
            'is_featured' => 'boolean',
            'stock_quantity' => 'nullable|integer|min:0',
            'images' => 'nullable|array',
        ]);

        $this->productRepository->update($id, $validated);

        return response()->json([
            'success' => true,
            'data' => $product->fresh('category'),
            'message' => 'Cập nhật sản phẩm thành công'
        ]);
    }

    /**
     * Remove the specified product
     */
    public function destroy(int $id)
    {
        $this->productRepository->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Xóa sản phẩm thành công'
        ]);
    }

    /**
     * Duplicate product
     */
    public function duplicate(int $id)
    {
        $newProduct = $this->productRepository->duplicate($id);

        return response()->json([
            'success' => true,
            'data' => $newProduct->load('category'),
            'message' => 'Nhân bản sản phẩm thành công'
        ]);
    }
}
