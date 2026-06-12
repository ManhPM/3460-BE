<?php

namespace App\Api\AdminV1\Services\Category;

use App\Api\AdminV1\Repositories\Category\CategoryRepositoryInterface;

class CategoryService
{
    protected $repository;

    public function __construct(CategoryRepositoryInterface $repository)
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
        if ($this->repository->hasProducts($id)) {
            throw new \Exception(__('category.cannot_delete_has_products'));
        }
        return $this->repository->delete($id);
    }
}

