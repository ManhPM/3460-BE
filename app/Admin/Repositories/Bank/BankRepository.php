<?php

namespace App\Admin\Repositories\Bank;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Bank;

class BankRepository extends EloquentRepository implements BankRepositoryInterface
{

    public function getModel(): string
    {
        return Bank::class;
    }

    public function getActiveBank($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->where('is_active', 1)->orderBy($column, $sort)->get();
        return $this->instance;
    }
}
