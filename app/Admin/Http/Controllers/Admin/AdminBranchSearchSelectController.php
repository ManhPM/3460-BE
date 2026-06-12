<?php

namespace App\Admin\Http\Controllers\Admin;

use App\Admin\Http\Controllers\BaseSearchSelectController;
use App\Admin\Repositories\Admin\AdminRepositoryInterface;

class AdminBranchSearchSelectController extends BaseSearchSelectController
{
    public function __construct(
        AdminRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    protected function data()
    {
        // Reuse repository searchAllLimit with role filter 'branch'
        $this->instance = $this->repository->searchAllLimit(
            $this->request->input('term', ''),
            $this->request->except('term', '_type', 'q'),
            ['id', 'branch_name', 'branch_phone', 'branch_address'],
            10,
            'branch'
        );
    }

    protected function selectResponse(): void
    {
        $this->instance = [
            'results' => collect($this->instance->items())->map(function ($admin) {
                return [
                    'id' => $admin->id,
                    'text' => trim(($admin->branch_name ?? '')) . ' - ' . (isset($admin->branch_phone) ? $admin->branch_phone : '') . ' - ' . (isset($admin->branch_address) ? $admin->branch_address : ''),
                ];
            }),
            'pagination' => [
                'more' => $this->instance->hasMorePages()
            ]
        ];
    }
}
