<?php

namespace App\Admin\Services\MembershipLevel;

use App\Admin\Services\MembershipLevel\MembershipLevelServiceInterface;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use Illuminate\Http\Request;

class MembershipLevelService implements MembershipLevelServiceInterface
{
    protected array $data;

    protected MembershipLevelRepositoryInterface $repository;

    public function __construct(
        MembershipLevelRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $data = $request->validated();
        return $this->repository->create($data);
    }

    public function update(Request $request)
    {
        $data = $request->validated();
        return $this->repository->update($data['id'], $data);
    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }
}
