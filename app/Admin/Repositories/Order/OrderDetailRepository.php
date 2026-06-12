<?php

namespace App\Admin\Repositories\Order;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Enums\Order\OrderStatus;
use App\Models\OrderDetail;
use App\Traits\HasRepositoryFromAdmin;

class OrderDetailRepository extends EloquentRepository implements OrderDetailRepositoryInterface
{
    use HasRepositoryFromAdmin;
    protected $select = [];

    public function getModel()
    {
        return OrderDetail::class;
    }

    public function getTotalEarningAffiliate($code)
    {
        $settinngRepository = $this->getSettingRepository();
        $returnAllowedDays = $settinngRepository->findByField('setting_key', 'return_allowed_days')->plain_value;
        $this->getQueryBuilder();

        $this->instance = $this->instance
            ->where('affiliate_code', $code)
            ->whereHas('order', function ($query) use ($returnAllowedDays) {
                $query->where('status', OrderStatus::Completed);
                $query->whereDate('created_at', '<=', now()->subDays($returnAllowedDays));
            });

        return $this->instance->sum('affiliate_earning');
    }

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
