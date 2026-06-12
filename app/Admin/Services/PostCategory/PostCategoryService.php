<?php

namespace App\Admin\Services\PostCategory;

use App\Admin\Services\PostCategory\PostCategoryServiceInterface;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostCategoryService implements PostCategoryServiceInterface
{

    protected array $data;

    protected PostCategoryRepositoryInterface $repository;
    protected PostRepositoryInterface $postRepository;

    public function __construct(
        PostCategoryRepositoryInterface $repository,
        PostRepositoryInterface $postRepository
    ) {
        $this->repository = $repository;
        $this->postRepository = $postRepository;
    }

    public function store(Request $request)
    {

        $data = $request->validated();
        $slug = Str::slug($data['name']);
        $originalSlug = $slug;
        $counter = 1;

        while (
            $this->repository->getQueryBuilder()->where('slug', $slug)->exists()
            ||
            $this->postRepository->getQueryBuilder()->where('slug', $slug)->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;

        return $this->repository->create($data);
    }


    public function update(Request $request): object|bool
    {

        $data = $request->validated();
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $data['slug'])) {
            return false;
        }
        if (
            $this->repository->getQueryBuilder()->where('slug', $data['slug'])->where('id', '!=', $data['id'])->exists()
            ||
            $this->postRepository->getQueryBuilder()->where('slug', $data['slug'])->exists()
        ) {
            return false;
        }
        return $this->repository->update($data['id'], $data);
    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }
}
