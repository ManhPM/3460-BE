<?php

namespace App\Api\AdminV1\Repositories\Attribute;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface AttributeRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
}

