<?php

namespace App\Api\V1\Repositories\Category;

use App\Admin\Repositories\Category\CategoryRepository as AdminCategoryRepository;
use App\Api\V1\Repositories\Category\CategoryRepositoryInterface;

class CategoryRepository extends AdminCategoryRepository implements CategoryRepositoryInterface
{
    public function getTree()
    {
        $keyword = request()->input('keyword', '');
        $limit = request()->input('limit', 10);

        $this->instance = $this->model->active()->orderBy('position', 'ASC');

        if (!empty($keyword)) {
            $this->instance = $this->instance->where(function ($query) use ($keyword) {
                $query->where('name', 'LIKE', '%' . $keyword . '%')
                    ->orWhereHas('posts', function ($postQuery) use ($keyword) {
                        $postQuery->where('title', 'LIKE', '%' . $keyword . '%');
                    });
            });
        }

        return $this->instance->simplePaginate($limit);
    }

    public function getHomeParentCategories()
    {
        $this->instance = $this->model->active()->where('parent_id', null)->orderBy('position', 'ASC')->limit(9);
        return $this->instance->get()->toTree();
    }

    public function getFlatTree($limit = 0)
    {
        $this->getQueryBuilderOrderBy('position', 'ASC');
        if ($limit) {
            $this->instance = $this->instance->where('is_active', true)->withDepth()
                ->limit($limit)
                ->get()
                ->toFlatTree();
        } else {
            $this->instance = $this->instance->where('is_active', true)->withDepth()
                ->get()
                ->toFlatTree();
        }
        return $this->instance;
    }

    public function getMobileHomeCategories()
    {
        $categories = $this->model->active()
            ->orderBy('position', 'ASC')
            ->where('is_home', 1)
            ->where('parent_id', null)
            ->has('products')
            ->get();

        $categories->each(function ($category) {
            $category->setRelation('products', $category->products()->limit(8)->get());
        });

        return $categories->toTree();
    }

    public function findByIdOrSlug($idOrSlug)
    {
        $this->instance = $this->model->whereIdOrSlug($idOrSlug)->firstOrFail();
        return $this->instance;
    }
    public function findByIdOrSlugWithAncestorsAndDescendants($idOrSlug)
    {
        $this->findByIdOrSlug($idOrSlug);

        $this->instance = $this->instance->load([
            'ancestors' => function ($query) {
                $query->defaultOrder();
            },
            'descendants'
        ]);
        return $this->instance;
    }

    public function getRootWithAllChildren()
    {
        $this->instance = $this->model->active()
            ->whereNull('parent_id')
            ->with(['descendants'])
            ->OrderBy('position', 'ASC')
            ->get();
        return $this->instance;
    }
}
