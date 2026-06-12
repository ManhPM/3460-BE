<?php

namespace App\Api\AdminV1\Services\Attribute;

use App\Api\AdminV1\Repositories\Attribute\AttributeRepositoryInterface;

class AttributeService
{
    protected $repository;

    public function __construct(AttributeRepositoryInterface $repository)
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

