<?php

namespace App\Api\AdminV1\Services\PostCategory;

use App\Api\AdminV1\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Api\AdminV1\Repositories\Post\PostRepositoryInterface;
use Illuminate\Support\Str;

class PostCategoryService
{
    protected $repository;
    protected $postRepository;

    public function __construct(
        PostCategoryRepositoryInterface $repository,
        PostRepositoryInterface $postRepository
    ) {
        $this->repository = $repository;
        $this->postRepository = $postRepository;
    }

    public function create(array $data)
    {
        if (empty($data['slug']) && !empty($data['name'])) {
            $slug = Str::slug($data['name']);
            $originalSlug = $slug;
            $counter = 1;

            $postCategoryModel = app($this->repository->getModel());
            $postModel = app($this->postRepository->getModel());

            while (
                $postCategoryModel->where('slug', $slug)->exists() ||
                $postModel->where('slug', $slug)->exists()
            ) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $data['slug'] = $slug;
        }

        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        if (isset($data['slug']) && !preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $data['slug'])) {
            throw new \InvalidArgumentException(__('post_category.slug_invalid'));
        }

        if (isset($data['slug'])) {
            $postCategoryModel = app($this->repository->getModel());
            $postModel = app($this->postRepository->getModel());

            if (
                $postCategoryModel->where('slug', $data['slug'])->where('id', '!=', $id)->exists() ||
                $postModel->where('slug', $data['slug'])->exists()
            ) {
                throw new \InvalidArgumentException(__('post_category.slug_exists'));
            }
        }

        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }
}

