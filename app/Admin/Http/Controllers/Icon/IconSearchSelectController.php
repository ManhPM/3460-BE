<?php

namespace App\Admin\Http\Controllers\Icon;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\Icon\IconRepositoryInterface;
use App\Admin\Http\Resources\Icon\IconSearchSelectResource;

class IconSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        IconRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function selectResponse()
    {
        $this->instance = [
            'results' => IconSearchSelectResource::collection($this->instance),
            'pagination' => [
                'more' => $this->instance->hasMorePages()
            ]
        ];
    }
}
