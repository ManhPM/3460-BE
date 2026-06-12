<?php

namespace App\Admin\Http\Controllers\User;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Http\Resources\User\UserSearchSelectResource;
use App\Admin\Repositories\User\UserRepositoryInterface;

class CustomerSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        UserRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }
    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => UserSearchSelectResource::collection($this->instance),
            'pagination' => [
                'more' => $this->instance->hasMorePages()
            ]
        ];
    }

    protected function data()
    {
        $this->instance = $this->repository->searchAllLimit(
            $this->request->input('term', ''),
            $this->request->except('term', '_type', 'q'),
            ['id', 'fullname', 'phone'], // fieldlist
            10, // limit
            'customer' // role
        );
    }
}
