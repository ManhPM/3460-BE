<?php

namespace App\Api\AdminV1\Services\Bank;

use App\Api\AdminV1\Repositories\Bank\BankRepositoryInterface;

class BankService
{
    protected $repository;

    public function __construct(BankRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        $bank = $this->repository->find($data['bank_id']);
        $data = array_merge($bank->toArray(), $data);
        unset($data['id'], $data['bank_id']);
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
}
