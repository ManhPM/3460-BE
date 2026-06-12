<?php

namespace App\Api\AdminV1\Services\Review;

use App\Api\AdminV1\Repositories\Review\ReviewRepositoryInterface;

class ReviewService
{
    protected $repository;

    public function __construct(ReviewRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function reply(int $id, string $reply)
    {
        return $this->repository->reply($id, $reply);
    }

    public function approve(int $id)
    {
        return $this->repository->approve($id);
    }

    public function reject(int $id)
    {
        return $this->repository->reject($id);
    }
}

