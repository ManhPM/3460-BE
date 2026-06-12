<?php

namespace App\Admin\Repositories\Voucher;

use App\Admin\Repositories\EloquentRepository;
use App\Models\Voucher;

class VoucherRepository extends EloquentRepository implements VoucherRepositoryInterface
{
    public function getModel(): string
    {
        return Voucher::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {

        $this->instance = $this->model->where('code', 'like', "%{$keySearch}%")->where('is_used', '!=', '0');

        foreach ($meta as $key => $value) {
            if ($key !== 'page') {
                $this->instance = $this->instance->where($key, $value);
            }
        }
        return $this->instance->paginate($limit);
    }

    public function getValid()
    {
        $this->instance = $this->model
            ->whereDate('date_end', '>=', now());

        return $this->instance->get();
    }

    public function getValidForUser($voucherType = null)
    {
        $limit = request()->input('limit', 10);
        $page = request()->input('page', 1);
        if ($voucherType) {
            $this->instance = $this->model->where('user_id', auth()->id())->where('voucher_type', $voucherType)
                ->whereDate('date_end', '>=', now());
        } else {
            $this->instance = $this->model->where('user_id', auth()->id())
                ->whereDate('date_end', '>=', now());
        }


        return $this->instance->where('is_used', 0)->paginate($limit, ['*'], 'page', $page);
    }
}
