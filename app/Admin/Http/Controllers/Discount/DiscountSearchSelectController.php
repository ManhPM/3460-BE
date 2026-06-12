<?php

namespace App\Admin\Http\Controllers\Discount;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Http\Resources\Discount\DiscountSearchSelectResource;

class DiscountSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        DiscountRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => DiscountSearchSelectResource::collection($this->instance),
            'pagination' => [
                'more' => $this->instance->hasMorePages()
            ]
        ];
    }
}
