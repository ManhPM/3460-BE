<?php

namespace App\Admin\Repositories\Section;

use App\Admin\Repositories\EloquentRepositoryInterface;
use App\Models\Section;

interface SectionRepositoryInterface extends EloquentRepositoryInterface
{
    public function findOrFailWithRelations($id, array $relations = ['categories']);
    public function attachCategories(Section $section, array $categoriesId);
    public function syncCategories(Section $section, array $categoriesId);
    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC');
}
