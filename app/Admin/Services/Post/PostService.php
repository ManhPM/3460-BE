<?php

namespace App\Admin\Services\Post;

use App\Admin\Services\Post\PostServiceInterface;
use App\Admin\Repositories\Post\PostRepositoryInterface;
use App\Admin\Repositories\PostCategory\PostCategoryRepositoryInterface;
use App\Enums\Post\PostType;
use App\Enums\PriorityStatus;
use App\Traits\UseLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Str;

class PostService implements PostServiceInterface
{
    use UseLog;


    protected array $data;

    protected PostRepositoryInterface $repository;
    protected PostCategoryRepositoryInterface $postCategoryRepository;

    public function __construct(
        PostRepositoryInterface $repository,
        PostCategoryRepositoryInterface $postCategoryRepository
    ) {
        $this->repository = $repository;
        $this->postCategoryRepository = $postCategoryRepository;
    }

    public function store(Request $request)
    {

        $data = $request->validated();
        $slug = Str::slug($data['title']);
        $originalSlug = $slug;
        $counter = 1;

        while (
            $this->repository->getQueryBuilder()->where('slug', $slug)->exists()
            ||
            $this->postCategoryRepository->getQueryBuilder()->where('slug', $slug)->exists()
        ) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $data['slug'] = $slug;
        $data['meta_title'] = $data['title'];
        $data['posted_at'] = now();
        $data['priority'] = PriorityStatus::NotPriority;
        $categoriesId = $data['categories_id'] ?? [];
        unset($data['categories_id']);
        DB::beginTransaction();
        try {
            $post = $this->repository->create($data);
            if ($categoriesId) {
                $this->repository->attachCategories($post, $categoriesId);
            }
            DB::commit();
            return $post;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to process create post CMS', $e);
            return false;
        }
    }

    public function update(Request $request): object|bool
    {
        $data = $request->validated();
        $current = $this->repository->getQueryBuilderOrderBy('id')->where('id', $data['id'])->first();
        if ($data['status'] == '1' && $current->status->value == '2') {
            $data['posted_at'] = now();
        }
        if (!preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $data['slug'])) {
            return false;
        }
        if (
            $this->repository->getQueryBuilder()->where('slug', $data['slug'])->where('id', '!=', $data['id'])->exists()
            ||
            $this->postCategoryRepository->getQueryBuilder()->where('slug', $data['slug'])->exists()
        ) {
            $this->logError('Failed to process update post CMS', new Exception('Slug đã tồn tại.'));
            return false;
        }
        $categoriesId = $data['categories_id'] ?? [];
        unset($data['categories_id']);
        DB::beginTransaction();
        try {
            $post = $this->repository->update($data['id'], $data);

            $this->repository->syncCategories($post, $categoriesId);
            DB::commit();
            return $post;
        } catch (Throwable $e) {
            DB::rollBack();
            $this->logError('Failed to process update post CMS', $e);
            return false;
        }
    }



    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }
}
