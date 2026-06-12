<?php

namespace App\Admin\Services\VoucherProgram;

use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use  App\Admin\Repositories\VoucherProgram\VoucherProgramRepositoryInterface;
use App\Admin\Traits\Setup;
use App\Enums\Notification\NotificationType;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class VoucherProgramService implements VoucherProgramServiceInterface
{
    use Setup;
    protected array $data;
    protected $voucherRepository;
    protected $userRepository;

    protected VoucherProgramRepositoryInterface $repository;

    public function __construct(
        VoucherProgramRepositoryInterface $repository,
        VoucherRepositoryInterface $voucherRepository,
        UserRepositoryInterface $userRepository,
    ) {
        $this->repository = $repository;
        $this->voucherRepository = $voucherRepository;
        $this->userRepository = $userRepository;
    }

    public function store(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $instance = $this->repository->create($this->data);
            DB::commit();

            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to create voucher progarm:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function update(Request $request): object|bool
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $instance = $this->repository->update($this->data['id'], $this->data);
            DB::commit();
            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to update voucher progarm:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }

    public function giveVoucher(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $instance = $this->repository->findOrFail($this->data['id']);
            // Tính ngày hết hạn = ngày hiện tại + số ngày hết hạn
            $this->data['date_end'] = now()->addDays($instance->expiration_days)->format('Y-m-d');
            $this->data['min_order_amount'] = $instance->min_order_amount;
            $this->data['type'] = $instance->type;
            $this->data['voucher_type'] = $instance->voucher_type;
            $this->data['discount_value'] = $instance->discount_value;
            $this->data['max_discount_value'] = $instance->max_discount_value;
            $this->data['avatar'] = $instance->avatar;
            if ($this->data['option'] == NotificationType::All->value) {
                $users = $this->userRepository->getAll();
                foreach ($users as $user) {
                    $this->data['user_id'] = $user->id;
                    for ($i = 0; $i < $instance->qty; $i++) {
                        $this->data['code'] = $this->createCodeVoucher();
                        $this->voucherRepository->create($this->data);
                    }
                }
            } else {
                foreach ($this->data['user_id'] as $userId) {
                    $this->data['user_id'] = $userId;
                    for ($i = 0; $i < $instance->qty; $i++) {
                        $this->data['code'] = $this->createCodeVoucher();
                        $this->voucherRepository->create($this->data);
                    }
                }
            }
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to give voucher to users:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function reset(Request $request)
    {
        DB::beginTransaction();

        try {
            $this->data = $request->validated();
            $instance = $this->repository->findOrFail($this->data['id']);

            // Lặp qua từng bản ghi và xóa từng cái một
            foreach ($instance->user_voucher_logs as $log) {
                $log->delete();
            }

            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollback();
            Log::error('Failed to reset voucher program to users:', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
