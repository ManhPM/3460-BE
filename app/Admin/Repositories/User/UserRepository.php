<?php

namespace App\Admin\Repositories\User;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\BaseAuthCMS;
use App\Admin\Traits\Roles;
use App\Models\User;

class UserRepository extends EloquentRepository implements UserRepositoryInterface
{
    use Roles;
    use BaseAuthCMS;

    protected $select = [];

    public function getModel(): string
    {
        return User::class;
    }

    public function getUserByRole($role)
    {
        return $this->model->whereHas('roles', function ($query) use ($role) {
            $query->where('name', $role);
        })->get();
    }

    public function searchAllLimit($keySearch = '', $meta = [], $select = ['id', 'fullname', 'phone'], $limit = 10, $role = 0)
    {
        $this->instance = $this->model->select($select)
            ->whereHas('roles', function ($query) {
                $query->where('name', $this->getRoleCustomer());
            });
        $this->getQueryBuilderFindByKey($keySearch);

        foreach ($meta as $key => $value) {
            if ($key !== 'page') {
                $this->instance = $this->instance->where($key, $value);
            }
        }

        if ($role) {
            $this->instance = $this->instance->whereHas('roles', function ($query) use ($role) {
                $query->where('name', $role);
            });
        }

        return $this->instance->paginate($limit);
    }

    protected function getQueryBuilderFindByKey($key): void
    {
        $this->instance = $this->instance->where(function ($query) use ($key) {
            return $query
                ->where('phone', 'LIKE', '%' . $key . '%')
                ->orWhere('email', 'LIKE', '%' . $key . '%')
                ->orWhere('fullname', 'LIKE', '%' . $key . '%');
        });
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
