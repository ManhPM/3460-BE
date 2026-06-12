<?php

namespace App\Admin\Repositories\VoucherProgram;

use App\Admin\Repositories\EloquentRepositoryInterface;


interface VoucherProgramRepositoryInterface extends EloquentRepositoryInterface
{
    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10);
    public function getValid();
    public function getValidForUser($userId);
}
