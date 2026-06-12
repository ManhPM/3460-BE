<?php

namespace App\Admin\Services\Section;

use App\Admin\Repositories\Admin\AdminRepositoryInterface;
use App\Admin\Repositories\Section\SectionRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Enums\Section\SectionType;
use App\Traits\NotifiesViaFirebase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SectionService implements SectionServiceInterface
{
    protected $data;

    protected $repository;

    public function __construct(
        SectionRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        try {
            DB::beginTransaction();
            $categoriesId = $this->data['categories_id'] ?? [];
            unset($this->data['categories_id']);
            $instance = $this->repository->create($this->data);
            $this->repository->attachCategories($instance, $categoriesId);
            DB::commit();
            return $instance;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            return false;
        }
    }

    public function update(Request $request): object|bool
    {
        $this->data = $request->validated();
        try {
            DB::beginTransaction();
            $categoriesId = $this->data['categories_id'] ?? [];
            unset($this->data['categories_id']);
            $instance = $this->repository->update($this->data['id'], $this->data);
            $this->repository->syncCategories($instance, $categoriesId);
            DB::commit();
            return $instance;
        } catch (\Throwable $th) {
            throw $th;
            DB::rollBack();
            return false;
        }
    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }
}
