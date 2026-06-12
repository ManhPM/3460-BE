<?php

namespace App\Admin\Http\Controllers\Voucher;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Http\Resources\Voucher\VoucherSearchSelectResource;

class VoucherSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        VoucherRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => VoucherSearchSelectResource::collection($this->instance),
            'pagination' => [
                'more' => $this->instance->hasMorePages()
            ]
        ];
    }
}
