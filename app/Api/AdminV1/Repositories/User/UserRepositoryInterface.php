<?php

namespace App\Api\AdminV1\Repositories\User;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface UserRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function search($searchTerm = null);
    public function findOrFailWithRelations($id);
    public function getUserOrders(int $userId, int $perPage = 10);
    public function getUserAddresses(int $userId);
}
