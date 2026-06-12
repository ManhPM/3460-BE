<?php

namespace App\Api\AdminV1\Services\Voucher;

use App\Api\AdminV1\Repositories\Voucher\VoucherRepositoryInterface;

class VoucherService
{
    protected $repository;

    public function __construct(VoucherRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function toggleStatus(int $id)
    {
        return $this->repository->toggleStatus($id);
    }
}

