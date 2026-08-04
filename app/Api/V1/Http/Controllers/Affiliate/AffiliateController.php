<?php

namespace App\Api\V1\Http\Controllers\Affiliate;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Traits\AuthService;
use App\Admin\Traits\Setup;
use App\Api\V1\Http\Requests\Affiliate\UpdateAffiliateRequest;
use App\Api\V1\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Api\V1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Api\V1\Support\Response;
use App\Enums\Transaction\WalletTransactionType;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * @group Cộng tác viên
 */
class AffiliateController extends Controller
{
    use AuthService, Response, Setup;

    protected $walletTransactionRepository;

    public function __construct(WalletTransactionRepositoryInterface $walletTransactionRepository)
    {
        $this->walletTransactionRepository = $walletTransactionRepository;
    }

    /**
     * Thông tin dashboard cộng tác viên
     *
     * Lấy thông tin cộng tác viên và thống kê theo tháng (tháng 1-12 năm nay).
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "affiliate_code": "AF981733846484",
     *          "wallet_balance": 450000,
     *          "bank_name": "VIETCOMBANK",
     *          "bank_account": "NGUYEN PHUC NHAN",
     *          "bank_account_number": "123123123123",
     *          "monthly_stats": [
     *              {
     *                  "month": 1,
     *                  "month_name": "Tháng 1",
     *                  "total_amount": 500000,
     *                  "transaction_count": 5
     *              },
     *              {
     *                  "month": 2,
     *                  "month_name": "Tháng 2",
     *                  "total_amount": 300000,
     *                  "transaction_count": 3
     *              }
     *          ]
     *      }
     * }
     */
    public function dashboard()
    {
        $user = $this->getCurrentUser();

        if (empty($user->affiliate_code)) {
            $user->affiliate_code = $this->createAffiliateCode();
            $user->save();
        }

        // Lấy thống kê hoa hồng cộng tác viên theo tháng (tháng 1-12 năm nay)
        $currentYear = Carbon::now()->year;
        $monthlyStats = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($currentYear, $month, 1)->startOfMonth();
            $endDate = Carbon::create($currentYear, $month, 1)->endOfMonth();

            $stats = WalletTransaction::where('user_id', $user->id)
                ->where('type', WalletTransactionType::Affiliate)
                ->whereIn('status', [WalletTransactionStatus::Approved, WalletTransactionStatus::Pending])
                ->whereBetween('created_at', [$startDate, $endDate])
                ->selectRaw('SUM(amount) as total_amount, COUNT(*) as transaction_count')
                ->first();

            $monthlyStats[] = [
                'month' => $month,
                'month_name' => "Tháng {$month}",
                'total_amount' => (float) ($stats->total_amount ?? 0),
                'transaction_count' => (int) ($stats->transaction_count ?? 0),
            ];
        }

        return $this->jsonResponseSuccess([
            'affiliate_code' => $user->affiliate_code,
            'wallet_balance' => (float) ($user->wallet_balance ?? 0),
            'bank_name' => $user->bank_name,
            'bank_account' => $user->bank_account,
            'bank_account_number' => $user->bank_account_number,
            'monthly_stats' => $monthlyStats,
        ], __('success'));
    }

    /**
     * Lấy 2 giao dịch gần nhất
     *
     * Lấy 2 giao dịch gần nhất của user (bất kể loại nào).
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 1,
     *              "amount": 100000,
     *              "type": "deposit",
     *              "status": "approved",
     *              "note": "Nạp tiền vào ví",
     *              "order_id": null,
     *              "proof_image": null,
     *              "created_at": "2025-01-30 10:30:00",
     *              "updated_at": "2025-01-30 10:30:00"
     *          },
     *          {
     *              "id": 2,
     *              "amount": -50000,
     *              "type": "withdraw",
     *              "status": "pending",
     *              "note": "Rút tiền từ ví",
     *              "order_id": null,
     *              "proof_image": null,
     *              "created_at": "2025-01-29 15:20:00",
     *              "updated_at": "2025-01-29 15:20:00"
     *          }
     *      ]
     * }
     */
    public function recentTransactions()
    {
        $user = $this->getCurrentUser();

        $transactions = WalletTransaction::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->limit(2)
            ->get();

        return $this->jsonResponseSuccess(
            WalletTransactionResource::collection($transactions),
            __('success')
        );
    }

    /**
     * Cập nhật thông tin cộng tác viên
     *
     * Cập nhật thông tin cộng tác viên: mã giới thiệu, thông tin ngân hàng.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam bank_name string nullable
     * Tên ngân hàng. Example: VIETCOMBANK
     *
     * @bodyParam bank_account string nullable
     * Tên chủ tài khoản. Example: NGUYEN PHUC NHAN
     *
     * @bodyParam bank_account_number string nullable
     * Số tài khoản ngân hàng. Example: 123123123123
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Cập nhật thông tin cộng tác viên thành công.",
     *      "data": {
     *          "affiliate_code": "AF981733846484",
     *          "bank_name": "VIETCOMBANK",
     *          "bank_account": "NGUYEN PHUC NHAN",
     *          "bank_account_number": "123123123123"
     *      }
     * }
     */
    public function update(UpdateAffiliateRequest $request)
    {
        $user = $this->getCurrentUser();
        $data = $request->validated();

        // Tự động sinh affiliate_code nếu user chưa có
        $updateData = [];

        if (!$user->affiliate_code) {
            $updateData['affiliate_code'] = $this->createAffiliateCode();
        }

        // Chỉ cập nhật các trường được gửi lên
        if (isset($data['bank_name'])) {
            $updateData['bank_name'] = $data['bank_name'];
        }

        if (isset($data['bank_account'])) {
            $updateData['bank_account'] = $data['bank_account'];
        }

        if (isset($data['bank_account_number'])) {
            $updateData['bank_account_number'] = $data['bank_account_number'];
        }

        if (empty($updateData)) {
            return $this->jsonResponseError('Không có dữ liệu để cập nhật.', 400);
        }

        $user->update($updateData);
        $user->refresh();

        return $this->jsonResponseSuccess([
            'affiliate_code' => $user->affiliate_code,
            'bank_name' => $user->bank_name,
            'bank_account' => $user->bank_account,
            'bank_account_number' => $user->bank_account_number,
        ], __('affiliate.update_success'));
    }
}
