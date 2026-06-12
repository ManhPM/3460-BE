<?php

namespace App\Api\AdminV1\Services\CommissionWithdrawal;

use App\Api\AdminV1\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;

class CommissionWithdrawalService
{
    protected $repository;

    public function __construct(CommissionWithdrawalRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function updateStatus(int $id, string $status, ?string $note = null)
    {
        $data = ['status' => $status];
        if ($note) {
            $data['note'] = $note;
        }
        return $this->repository->update($id, $data);
    }
}

