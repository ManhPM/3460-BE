<?php

namespace App\Admin\Repositories\Category;
use App\Admin\Repositories\EloquentRepositoryInterface;

interface CategoryRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFlatTreeNotInNode(array $nodeId);

    public function getFlatTree($limit = 0);

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');

    public function getParentCategory();

}
