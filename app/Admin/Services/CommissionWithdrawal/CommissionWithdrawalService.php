<?php

namespace App\Admin\Services\CommissionWithdrawal;

use  App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Enums\Order\PaymentStatus;
use App\Enums\WithdrawStatus;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommissionWithdrawalService implements CommissionWithdrawalServiceInterface
{
    use AuthService;
    protected array $data;

    protected CommissionWithdrawalRepositoryInterface $repository;

    public function __construct(
        CommissionWithdrawalRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }


    public function update(Request $request): object|bool
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $instance = $this->repository->findOrFail($data['id']);
            $data['processed_at'] = Carbon::now();
            $instance->update($data);
            DB::commit();
            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
    }


    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }



    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $user = $this->getCurrentUser();
            $data['user_id'] = $user->id;
            $data['status'] = WithdrawStatus::Pending->value;
            $instance = $this->repository->create($data);
            DB::commit();

            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            return false;
        }
    }
}
