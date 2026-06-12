<?php

namespace App\Admin\Repositories\Icon;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Icon\IconRepositoryInterface;
use App\Models\Icon;

class IconRepository extends EloquentRepository implements IconRepositoryInterface
{
    public function getModel()
    {
        return Icon::class;
    }
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {

        $this->instance = $this->model->where('name', 'like', "%{$keySearch}%");

        foreach ($meta as $key => $value) {
            if ($key !== 'page') {
                $this->instance = $this->instance->where($key, $value);
            }
        }
        return $this->instance->paginate($limit);
    }
}
