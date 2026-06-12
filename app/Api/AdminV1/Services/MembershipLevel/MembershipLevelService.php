<?php

namespace App\Api\AdminV1\Services\MembershipLevel;

use App\Api\AdminV1\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;

class MembershipLevelService
{
    protected $repository;

    public function __construct(MembershipLevelRepositoryInterface $repository)
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
}

