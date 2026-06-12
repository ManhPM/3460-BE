<?php

namespace App\Api\AdminV1\Repositories\Slider;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Slider;

class SliderRepository extends EloquentRepository implements SliderRepositoryInterface
{
    public function getModel(): string
    {
        return Slider::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with('items');

        // Column-specific filters - name
        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', '%' . request('name') . '%');
        }

        // Column-specific filters - plain_key
        if (request()->has('plain_key') && !empty(request('plain_key'))) {
            $query->where('plain_key', 'like', '%' . request('plain_key') . '%');
        }

        // Column-specific filters - status (SELECT - exact match)
        if (request()->has('status') && request('status') !== '' && request('status') !== null) {
            $query->where('status', request('status'));
        }

        // Column-specific filters - created_at (DATE)
        if (request()->has('created_at') && !empty(request('created_at'))) {
            $query->where('created_at', 'like', '%' . request('created_at') . '%');
        }

        // Column-specific filters - updated_at (DATE)
        if (request()->has('updated_at') && !empty(request('updated_at'))) {
            $query->where('updated_at', 'like', '%' . request('updated_at') . '%');
        }

        $sortBy = request('sort_by', 'created_at');
        $sortOrder = request('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $perPage = request('per_page', 10);
        return $query->paginate($perPage);
    }

    public function findOrFailWithRelations($id)
    {
        return $this->model->with('items')->findOrFail($id);
    }
}

