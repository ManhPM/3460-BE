<?php

namespace App\Api\AdminV1\Repositories\Review;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface ReviewRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function reply(int $id, string $reply);
    public function approve(int $id);
    public function reject(int $id);
}
