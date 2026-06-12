<?php

namespace App\Http\Controllers\VoucherProgram;

use App\Admin\Repositories\UserVoucherLog\UserVoucherLogRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Http\Controllers\Controller;
use App\Admin\Traits\AuthService;
use App\Admin\Repositories\VoucherProgram\VoucherProgramRepositoryInterface;
use App\Admin\Traits\Setup;
use App\Api\V1\Support\Response;
use App\Traits\UseLog;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VoucherProgramController extends Controller
{
    use AuthService, Response, Setup, UseLog;

    protected $voucherRepository;
    protected $userVoucherLogRepository;

    public function __construct(
        VoucherProgramRepositoryInterface $repository,
        VoucherRepositoryInterface $voucherRepository,
        UserVoucherLogRepositoryInterface $userVoucherLogRepository,
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->voucherRepository = $voucherRepository;
        $this->userVoucherLogRepository = $userVoucherLogRepository;
    }

    public function getView(): array
    {
        return [
            'index' => 'user.voucher_programs.index',
        ];
    }

    public function index()
    {
        $user = $this->getCurrentUser();
        $voucherPrograms = $this->repository->getValidForUser($user->id);
        return view($this->view['index'], [
            'voucherPrograms' => $voucherPrograms,
            'breadcrumbs' => $this->homeCrums->add(__('Thu thập voucher'))->getBreadcrumbs()
        ]);
    }
    public function collect(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getCurrentUser();
            $voucherProgram = $this->repository->find($request->input('voucher_program_id'));

            // Kiểm tra nếu không có user hoặc voucherProgram thì trả về lỗi
            if (!$user || !$voucherProgram) {
                return $this->jsonResponseError('Yêu cầu không hợp lệ');
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
