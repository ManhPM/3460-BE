<?php

namespace App\Api\V1\Repositories\Category;

interface CategoryRepositoryInterface
{
    public function getTree();
    public function getMobileHomeCategories();

    public function findByIdOrSlug($idOrSlug);

    public function findByIdOrSlugWithAncestorsAndDescendants($idOrSlug);

    public function getRootWithAllChildren();
    public function getFlatTree($limit = 0);
    public function getHomeParentCategories();
}
