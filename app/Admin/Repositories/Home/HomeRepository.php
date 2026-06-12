<?php

namespace App\Admin\Repositories\Section;

use App\Admin\Repositories\EloquentRepository;
use App\Admin\Repositories\Section\SectionRepositoryInterface;
use App\Models\Section;

class SectionRepository extends EloquentRepository implements SectionRepositoryInterface
{

    protected $select = [];

    public function getModel()
    {
        return Section::class;
    }
    public function findOrFailWithRelations($id, array $relations = ['categories'])
    {
        $this->findOrFail($id);
        $this->instance = $this->instance->load($relations);
        return $this->instance;
    }

    public function attachCategories(Section $post, array $categoriesId)
    {
        return $post->categories()->attach($categoriesId);
    }

    public function syncCategories(Section $post, array $categoriesId)
    {
        return $post->categories()->sync($categoriesId);
    }

    public function getQueryBuilderOrderBy($column = 'id', $sort = 'DESC')
    {
        $this->getQueryBuilder();
        $this->instance = $this->instance->orderBy($column, $sort);
        return $this->instance;
    }
}
