<?php

namespace App\Admin\Repositories\UserVoucherLog;

use App\Admin\Repositories\EloquentRepository;
use App\Models\UserVoucherLog;

class UserVoucherLogRepository extends EloquentRepository implements UserVoucherLogRepositoryInterface
{
    public function getModel(): string
    {
        return UserVoucherLog::class;
    }
}
