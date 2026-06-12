<?php

namespace App\Admin\Repositories\ShoppingCart;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use App\Models\ShoppingCart;
use Illuminate\Database\Eloquent\Collection;

class ShoppingCartRepository extends EloquentRepository implements ShoppingCartRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return ShoppingCart::class;
    }

    public function findManyById(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }
}
