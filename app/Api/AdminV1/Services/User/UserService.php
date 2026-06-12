<?php

namespace App\Api\AdminV1\Services\User;

use App\Api\AdminV1\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\Hash;

class UserService
{
    protected $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        // Tự động gán hạng id = 1 cho user mới nếu chưa có membership_id
        if (!isset($data['membership_id'])) {
            $data['membership_id'] = 1;
        }
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}

