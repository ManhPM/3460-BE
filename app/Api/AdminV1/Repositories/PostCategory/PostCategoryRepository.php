<?php

namespace App\Api\AdminV1\Repositories\PostCategory;

use App\Admin\Repositories\EloquentRepository;
use App\Models\PostCategory;

class PostCategoryRepository extends EloquentRepository implements PostCategoryRepositoryInterface
{
    public function getModel(): string
    {
        return PostCategory::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with(['parent', 'children']);

        // Column-specific filters
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        if (request()->has('desc') && !empty(request('desc'))) {
            $query->where('desc', 'like', "%" . request('desc') . "%");
        }

        if (request()->has('slug') && !empty(request('slug'))) {
            $query->where('slug', 'like', "%" . request('slug') . "%");
        }

        if (request()->has('parent_id') && request('parent_id') !== '' && request('parent_id') !== null) {
            $query->where('parent_id', request('parent_id'));
        }

        if (request()->has('is_home') && request('is_home') !== '' && request('is_home') !== null) {
            $query->where('is_home', request('is_home'));
        }

        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', "%" . request('created_at') . "%");
        }

        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', "%" . request('updated_at') . "%");
        }

        // Pagination
        $perPage = request('per_page', 15);

        // Sort by position, then by name
        return $query->orderBy('position', 'asc')
            ->orderBy('name', 'asc')
            ->paginate($perPage);
    }
}

