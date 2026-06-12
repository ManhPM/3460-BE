<?php

namespace App\Api\AdminV1\Repositories\Attribute;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Attribute;

class AttributeRepository extends EloquentRepository implements AttributeRepositoryInterface
{
    public function getModel(): string
    {
        return Attribute::class;
    }

    public function getFiltered()
    {
        $query = $this->model->newQuery()->with(['variations']);

        // Column-specific filters
        if (request()->has('id') && !empty(request('id'))) {
            $query->where('id', 'like', "%" . request('id') . "%");
        }

        if (request()->has('name') && !empty(request('name'))) {
            $query->where('name', 'like', "%" . request('name') . "%");
        }

        // Select/Dropdown - Exact match
        if (request()->has('type') && request('type') !== '' && request('type') !== null) {
            $query->where('type', request('type'));
        }

        if (request()->has('desc') && !empty(request('desc'))) {
            $query->where('desc', 'like', "%" . request('desc') . "%");
        }

        if (request()->has('position') && !empty(request('position'))) {
            $query->where('position', request('position'));
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
}

