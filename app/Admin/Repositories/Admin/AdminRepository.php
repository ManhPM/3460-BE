<?php

namespace App\Admin\Repositories\Admin;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Traits\BaseAuthCMS;
use App\Models\Admin;

class AdminRepository extends EloquentRepository implements AdminRepositoryInterface
{
    use BaseAuthCMS;

    protected $select = [];

    public function getModel(): string
    {
        return Admin::class;
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->with('roles')->orderBy($column, $sort);
        return $this->instance;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'name', 'email'], $limit = 10, $role = null)
    {
        $this->instance = $this->model->select($select);

        if ($role) {
            $this->instance = $this->instance->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        if ($keySearch) {
            $this->instance = $this->instance->where(function ($q) use ($keySearch) {
                $q->where('branch_name', 'LIKE', '%' . $keySearch . '%');
                $q->orWhere('branch_phone', 'LIKE', '%' . $keySearch . '%');
                $q->orWhere('branch_address', 'LIKE', '%' . $keySearch . '%');
            });
        }

        foreach ($meta as $key => $value) {
            if ($key !== 'page') {
                $this->instance = $this->instance->where($key, $value);
            }
        }

        return $this->instance->paginate($limit);
    }
}
