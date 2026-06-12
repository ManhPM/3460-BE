<?php

namespace App\Api\V1\Http\Controllers\VoucherProgram;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\UserVoucherLog\UserVoucherLogRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Repositories\VoucherProgram\VoucherProgramRepositoryInterface;
use App\Admin\Services\VoucherProgram\VoucherProgramServiceInterface;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Setup;
use App\Api\V1\Http\Resources\VoucherProgram\AllVoucherProgramResource;
use App\Api\V1\Support\Response;
use App\Traits\UseLog;
use Exception;
use Illuminate\Support\Facades\DB;

/**
 * @group Voucher
 */
class VoucherProgramController extends Controller
{
    use Response, Setup, AuthService, UseLog;

    protected $userVoucherLogRepository;
    protected $voucherRepository;

    public function __construct(
        VoucherProgramRepositoryInterface $repository,
        VoucherRepositoryInterface $voucherRepository,
        UserVoucherLogRepositoryInterface $userVoucherLogRepository,
        VoucherProgramServiceInterface $service,

    ) {
        $this->service = $service;
        $this->repository = $repository;
        $this->userVoucherLogRepository = $userVoucherLogRepository;
        $this->voucherRepository = $voucherRepository;
    }
    public function index()
    {
        $user = $this->getCurrentUser();
        $voucherPrograms = $this->repository->getValidForUser($user->id);
        return $this->jsonResponseSuccess(new AllVoucherProgramResource($voucherPrograms));
    }

    public function collect($id)
    {
        DB::beginTransaction();
        try {
            $user = $this->getCurrentUser();
            $voucherProgram = $this->repository->find($id);

            // Kiểm tra nếu không có user hoặc voucherProgram thì trả về lỗi
            if (!$user || !$voucherProgram) {
                return $this->jsonResponseError(__('request_invalid'));
            }

            // Kiểm tra xem user đã collect voucher này chưa
            $existingLog = $voucherProgram->user_voucher_logs()->where('user_id', $user->id)->first();

            if ($existingLog) {
                return $this->jsonResponseError('Bạn đã thu thập voucher này rồi');
            }

            // Tạo log mới cho user
            $this->userVoucherLogRepository->create([
                'user_id' => $user->id,
                'voucher_program_id' => $voucherProgram->id,
            ]);

            // Tiến hành tạo voucher từ thông tin của voucherProgram
            // Tính ngày hết hạn = ngày hiện tại + số ngày hết hạn
            $data['date_end'] = now()->addDays($voucherProgram->expiration_days)->format('Y-m-d');
            $data['min_order_amount'] = $voucherProgram->min_order_amount;
            $data['type'] = $voucherProgram->type;
            $data['voucher_type'] = $voucherProgram->voucher_type;
            $data['discount_value'] = $voucherProgram->discount_value;
            $data['max_discount_value'] = $voucherProgram->max_discount_value;
            $data['avatar'] = $voucherProgram->avatar;

            $data['user_id'] = $user->id;
            for ($i = 0; $i < $voucherProgram->qty; $i++) {
                $data['code'] = $this->createCodeVoucher();
                $this->voucherRepository->create($data);
            }

            // Trả về phản hồi thành công
            DB::commit();
            return $this->jsonResponseSuccess(null);
        } catch (Exception $e) {
            DB::rollBack();
            $this->logError('Failed to collect voucher: ', $e);
            return $this->jsonResponseError('Đã xảy ra lỗi, vui lòng thử lại sau');
        }
    }
}
