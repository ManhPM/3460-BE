<?php

namespace App\Admin\Repositories\Ward;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use App\Models\Ward;

class WardRepository extends EloquentRepository implements WardRepositoryInterface
{
    public function getModel()
    {
        return Ward::class;
    }

    public function searchAllLimit($keySearch = '', $provinceId = 0, $limit = 10)
    {
        $this->instance = $this->model->where('name', 'like', "%{$keySearch}%");
        if ($provinceId) {
            $this->instance = $this->instance->where('province_id', $provinceId);
        }
        return $this->instance->paginate($limit);
    }
}
