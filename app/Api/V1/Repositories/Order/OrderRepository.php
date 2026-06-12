<?php

namespace App\Api\V1\Repositories\Order;

use App\Admin\Repositories\Order\OrderRepository as AdminOrderRepository;
use App\Admin\Traits\AuthService;
use App\Api\V1\Repositories\Order\OrderRepositoryInterface;
use App\Enums\Order\OrderStatus;

class OrderRepository extends AdminOrderRepository implements OrderRepositoryInterface
{
    use AuthService;
    public function getByKeyAuthCurrent($filter)
    {
        $this->instance = $this->model->currentAuth()
            ->with('details');
        foreach ($filter as $column => $value) {
            if (in_array($column, ['limit', 'page'])) {
                continue;
            }
            $this->instance = $this->instance->where($column, $value);
        }
        $limit = request()->input('limit', 10);
        $this->instance = $this->instance->orderBy('id', 'desc')->paginate($limit);
        return $this->instance;
    }

    public function getAffiliateOrders($filter)
    {
        $user = $this->getCurrentUser();
        $this->instance = $this->model->where('affiliate_code', $user->affiliate_code)->where('status', OrderStatus::Completed);
        foreach ($filter as $column => $value) {
            $this->instance = $this->instance->where($column, $value);
        }
        $this->instance = $this->instance->orderBy('id', 'desc')->get();
        return $this->instance;
    }

    public function findOrFail($id)
    {
        $this->instance = $this->model->findOrFail($id);

        $this->authorize('view', 'user');

        return $this->instance;
    }
}
