<?php

namespace App\Api\AdminV1\Repositories\Slider;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface SliderRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function findOrFailWithRelations($id);
}

