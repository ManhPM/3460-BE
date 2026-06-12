<?php

namespace App\Api\AdminV1\Repositories\Product;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Product;
use Illuminate\Support\Str;

class ProductRepository extends EloquentRepository implements ProductRepositoryInterface
{
    public function getModel(): string
    {
        return Product::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with(['categories', 'productVariations', 'productVariations.attribute_variations', 'productVariations.attribute_variations.attribute'])->where('is_deleted', 0);

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        if (request()->has('sku') && !empty(request('sku'))) {
            $query->where('sku', 'like', "%" . request('sku') . "%");
        }

        // Select/Dropdown - Exact match
        if (request()->has('type') && request('type') !== '' && request('type') !== null) {
            $query->where('type', request('type'));
        }

        if (request()->has('is_active') && request('is_active') !== '' && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }

        if (request()->has('is_featured') && request('is_featured') !== '' && request('is_featured') !== null) {
            $query->where('is_featured', request('is_featured'));
        }

        // Filter by category (many-to-many) - support both ID and name search
        if (request()->has('category_id') && request('category_id') !== '' && request('category_id') !== null) {
            $categoryId = request('category_id');
            // If it's numeric, search by ID, otherwise search by name
            if (is_numeric($categoryId)) {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.id', $categoryId);
                });
            } else {
                $query->whereHas('categories', function ($q) use ($categoryId) {
                    $q->where('categories.name', 'like', '%' . $categoryId . '%');
                });
            }
        }

        // Price filters
        if (request()->has('price') && !empty(request('price'))) {
            $query->where('price', request('price'));
        }

        if (request()->has('promotion_price') && !empty(request('promotion_price'))) {
            $query->where('promotion_price', request('promotion_price'));
        }

        if (request()->has('qty') && !empty(request('qty'))) {
            $query->where('qty', request('qty'));
        }

        // Date/Datetime - Dùng like
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        return $query->orderBy('id', 'desc')->paginate($perPage);
    }

    public function findOrFailWithRelations($id)
    {
        return $this->model->with([
            'categories',
            'productVariations',
            'productVariations.attribute_variations',
            'productVariations.attribute_variations.attribute',
            'productVariations.attributeVariations',
            'productVariations.attributeVariations.attribute',
            'productAttributes',
            'productAttributes.attribute',
            'productAttributes.attribute_variations',
            'productAttributes.attributeVariations'
        ])->findOrFail($id);
    }

    public function create(array $data)
    {
        // Auto-generate slug if not provided
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }

        return parent::create($data);
    }

    public function duplicate(int $id)
    {
        $product = $this->model->findOrFail($id);
        $newProduct = $product->replicate();
        $newProduct->name = $product->name . ' (Copy)';
        $newProduct->slug = $product->slug . '-copy-' . time();
        $newProduct->sku = $product->sku . '-COPY-' . time();
        $newProduct->status = 'draft';
        $newProduct->save();

        return $newProduct;
    }
}
