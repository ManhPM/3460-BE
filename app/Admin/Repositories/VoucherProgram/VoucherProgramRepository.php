<?php

namespace App\Admin\Repositories\VoucherProgram;

use App\Admin\Repositories\EloquentRepository;
use App\Models\VoucherProgram;
use Illuminate\Support\Facades\DB;

class VoucherProgramRepository extends EloquentRepository implements VoucherProgramRepositoryInterface
{
    public function getModel(): string
    {
        return VoucherProgram::class;
    }

    public function searchAllLimit($keySearch = '', $meta = [], $limit = 10)
    {

        $this->instance = $this->model->where('code', 'like', "%{$keySearch}%")->where('is_used', '!=', '0');

        foreach ($meta as $key => $value) {
            if ($key !== 'page') {
                $this->instance = $this->instance->where($key, $value);
            }
        }
        return $this->instance->get();
    }

    public function getValid()
    {
        // Không cần filter theo date nữa vì không có date_start, date_end
        $this->instance = $this->model;

        return $this->instance->get();
    }

    public function getValidForUser($userId)
    {
        $limit = request()->input('limit', 10);
        $page = request()->input('page', 1);
        $type = request()->input('voucher_type', 'product');
        $this->instance = $this->model->leftJoin('user_voucher_logs', function ($join) use ($userId) {
            $join->on('voucher_programs.id', '=', 'user_voucher_logs.voucher_program_id')
                ->where('user_voucher_logs.user_id', $userId);
        })
            ->select(
                'voucher_programs.*',
                DB::raw('IF(user_voucher_logs.id IS NULL, 0, 1) as is_collected')
            );

        return $this->instance->where('voucher_type', $type)->paginate($limit, ['*'], 'page', $page);
    }
}
