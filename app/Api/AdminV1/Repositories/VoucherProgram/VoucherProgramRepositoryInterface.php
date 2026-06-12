<?php

namespace App\Api\AdminV1\Repositories\VoucherProgram;

use App\Admin\Repositories\EloquentRepositoryInterface;

interface VoucherProgramRepositoryInterface extends EloquentRepositoryInterface
{
    public function getFiltered();
    public function toggleStatus(int $id);
}

