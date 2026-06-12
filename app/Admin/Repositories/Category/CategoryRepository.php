<?php

namespace App\Admin\Repositories\Category;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Category\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryRepository extends EloquentRepository implements CategoryRepositoryInterface
{

    protected $select = [];

    public function getModel(): string
    {
        return Category::class;
    }
    public function getFlatTreeNotInNode(array $nodeId)
    {
        $this->getQueryBuilderOrderBy('position', 'ASC');
        $this->instance = $this->instance->whereNotIn('id', $nodeId)
            ->withDepth()
            ->get()
            ->toFlatTree();
        return $this->instance;
    }
    public function getFlatTree($limit = 0)
    {
        $this->getQueryBuilderOrderBy('position', 'ASC');
        if ($limit) {
            $this->instance = $this->instance->withDepth()
                ->limit($limit)
                ->get()
                ->toFlatTree();
        } else {
            $this->instance = $this->instance->withDepth()
                ->get()
                ->toFlatTree();
        }
        return $this->instance;
    }

    public function getParentCategory()
    {
        $this->getQueryBuilderOrderBy('position', 'ASC');
        $this->instance = $this->instance
            ->where('parent_id', null)
            ->get();
        return $this->instance;
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
