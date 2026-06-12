<?php

namespace App\Admin\Http\Controllers\Province;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Admin\Http\Resources\Province\ProvinceSearchSelectResource;

class ProvinceSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        ProvinceRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse()
    {
        $this->instance = [
            'results' => ProvinceSearchSelectResource::collection($this->instance),
            'pagination' => [
                'more' => $this->instance->hasMorePages()
            ]
        ];
    }
}
