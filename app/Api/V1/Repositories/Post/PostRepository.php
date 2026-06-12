<?php

namespace App\Api\V1\Repositories\Post;

use App\Admin\Repositories\Post\PostRepository as AdminPostRepository;
use App\Api\V1\Repositories\Post\PostRepositoryInterface;

class PostRepository extends AdminPostRepository implements PostRepositoryInterface
{
    public function getByCategoriesIdsPaginate(array $categoriesIds, $page = 1, $limit = 4)
    {
        $keyword = request()->input('keyword', '');
        $limit = request()->input('limit', 10);
        $page = $page ? $page - 1 : 0;

        $query = $this->model->published()->hasCategories($categoriesIds);

        if ($keyword) {
            $query->where('title', 'LIKE', '%' . $keyword . '%');
        }

        return $query
            ->offset($page * $limit)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();
    }

    public function findByPublished($id)
    {
        $this->instance = $this->model->where('id', $id)
            ->published()
            ->firstOrFail();

        return $this->instance;
    }
    public function paginate($page = 1, $limit = 10, $category_id = null)
    {
        $page = $page ? $page - 1 : 0;

        $query = $this->model->published();

        if ($category_id) {
            $query->whereHas('categories', function ($query) use ($category_id) {
                $query->where('id', $category_id);
            });
        }

        $this->instance = $query->offset($page * $limit)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        return $this->instance;
    }


    public function getFeaturedPaginate($page = 1, $limit = 10)
    {
        $page = $page ? $page - 1 : 0;
        $this->instance = $this->model->published()
            ->featured()
            ->offset($page * $limit)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();
        return $this->instance;
    }

    public function getRelated($id, $page = 1, $limit = 10)
    {
        $this->findByPublished($id);

        $this->instance = $this->instance->load(['categories:id']);
        $page = $page ? $page - 1 : 0;
        $categoriesId = $this->instance->categories->pluck('id')->all();
        $this->instance = $this->model->published()
            ->whereNotIn('id', [$id])
            ->hasCategories($categoriesId)
            ->offset($page * $limit)
            ->limit($limit)
            ->orderBy('id', 'desc')
            ->get();

        return $this->instance;
    }
}
