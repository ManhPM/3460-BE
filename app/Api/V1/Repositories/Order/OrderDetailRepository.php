<?php

namespace App\Api\V1\Repositories\Order;

use App\Admin\Repositories\Order\OrderDetailRepository as AdminOrderDetailRepository;
use App\Api\V1\Repositories\Order\OrderDetailRepositoryInterface;
use App\Enums\Order\OrderStatus;

class OrderDetailRepository extends AdminOrderDetailRepository implements OrderDetailRepositoryInterface
{
    public function getAffiliate($code)
    {
        $settinngRepository = $this->getSettingRepository();
        $returnAllowedDays = $settinngRepository->findByField('setting_key', 'return_allowed_days')->plain_value;

        $this->instance = $this->model->where('affiliate_code', $code)->whereHas('order', function ($subQuery) use ($returnAllowedDays) {
            $subQuery->where('status', OrderStatus::Completed);
            $subQuery->whereDate('created_at', '<=', now()->subDays($returnAllowedDays));
        })->paginate(8);
        return $this->instance;
    }
}
