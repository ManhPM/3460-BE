<?php

namespace App\Api\AdminV1\Repositories\Category;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Category;
use Illuminate\Support\Str;

class CategoryRepository extends EloquentRepository implements CategoryRepositoryInterface
{
    public function getModel(): string
    {
        return Category::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery();

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        if (request()->has('slug') && !empty(request('slug'))) {
            $query->where('slug', 'like', "%" . request('slug') . "%");
        }

        // Select/Dropdown - Exact match
        if (request()->has('parent_id') && request('parent_id') !== '' && request('parent_id') !== null) {
            $query->where('parent_id', request('parent_id'));
        }

        if (request()->has('is_active') && request('is_active') !== '' && request('is_active') !== null) {
            $query->where('is_active', request('is_active'));
        }

        if (request()->has('is_home') && request('is_home') !== '' && request('is_home') !== null) {
            $query->where('is_home', request('is_home'));
        }

        if (request()->has('position') && !empty(request('position'))) {
            $query->where('position', request('position'));
        }

        if (request()->has('description') && !empty(request('description'))) {
            $query->where('description', 'like', "%" . request('description') . "%");
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

        return $query->orderBy('position', 'asc')->paginate($perPage);
    }


    public function create(array $data)
    {
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        return parent::create($data);
    }

    public function hasProducts(int $id): bool
    {
        $category = $this->model->findOrFail($id);
        return $category->products()->count() > 0;
    }
}
