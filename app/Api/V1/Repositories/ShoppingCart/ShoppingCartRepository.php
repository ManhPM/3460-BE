<?php

namespace App\Api\V1\Repositories\ShoppingCart;

use App\Admin\Repositories\ShoppingCart\ShoppingCartRepository as AdminShoppingCartRepository;
use App\Api\V1\Repositories\ShoppingCart\ShoppingCartRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ShoppingCartRepository extends AdminShoppingCartRepository implements ShoppingCartRepositoryInterface
{
    public function findManyById(array $ids): Collection
    {
        return $this->model->whereIn('id', $ids)->get();
    }
    public function getAuthCurrent()
    {
        $this->instance = $this->model->currentAuth()
            ->with(['product', 'productVariation.attribute_variations', 'admin'])
            ->orderBy('id', 'desc')
            ->get();

        return $this->instance;
    }

    public function updateOrCreate($compare, $data)
    {
        $this->instance = $this->model->updateOrCreate($compare, $data);
        return $this->instance;
    }

    public function updateMultiple(array $ids, array $qty)
    {
        $arrayDelete = [];
        foreach ($ids as $key => $id) {
            if (isset($qty[$key])) {
                if ($qty[$key] > 0) {
                    $this->model->currentAuth()->where('id', $id)->update([
                        'qty' => $qty[$key]
                    ]);
                } else {
                    $arrayDelete[] = $id;
                }
            }
        }
        $this->deleteMultiple($arrayDelete);
        return true;
    }
    public function deleteAllCurrentAuth()
    {
        return $this->model->currentAuth()->delete();
    }
    public function deleteMultiple(array $ids)
    {
        if (count($ids) > 0) {
            $this->model->currentAuth()->whereIn('id', $ids)->delete();
        }
        return true;
    }
}
