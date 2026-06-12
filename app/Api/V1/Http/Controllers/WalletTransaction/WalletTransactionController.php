<?php

namespace App\Api\V1\Http\Controllers\WalletTransaction;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService;
use App\Api\V1\Http\Requests\WalletTransaction\DepositWalletRequest;
use App\Api\V1\Http\Requests\WalletTransaction\WalletTransactionListRequest;
use App\Api\V1\Http\Requests\WalletTransaction\WithdrawWalletRequest;
use App\Api\V1\Http\Resources\PaginationResource;
use App\Api\V1\Http\Resources\WalletTransaction\WalletTransactionCollection;
use App\Api\V1\Http\Resources\WalletTransaction\WalletTransactionResource;
use App\Api\V1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Api\V1\Support\Response;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * @group Giao dịch ví
 */
class WalletTransactionController extends Controller
{
    use AuthService, Response;

    protected $repository;
    protected $fileService;

    public function __construct(
        WalletTransactionRepositoryInterface $repository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->fileService = $fileService;
    }

    /**
     * Danh sách giao dịch ví
     *
     * Lấy danh sách giao dịch ví của user theo loại.
     *
     * <strong>Các loại giao dịch:</strong>
     * + deposit: Nạp tiền
     * + withdraw: Rút tiền
     * + payment: Thanh toán
     * + refund: Hoàn tiền
     *
     * @queryParam type string
     * Loại giao dịch (deposit, withdraw, payment, refund, affiliate). Example: deposit
     *
     * @queryParam page integer
     * Số trang (mặc định: 1). Example: 1
     *
     * @queryParam limit integer
     * Số lượng mỗi trang (mặc định: 10, tối đa: 100). Example: 10
     *
     * @queryParam period string
     * Khoảng thời gian (today, this_week, this_month, this_year, custom). Example: this_month
     *
     * @queryParam start_date date
     * Ngày bắt đầu (bắt buộc nếu period=custom). Example: 2025-01-01
     *
     * @queryParam end_date date
     * Ngày kết thúc (bắt buộc nếu period=custom, phải >= start_date). Example: 2025-01-31
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
     *              "created_at": "2025-01-15 10:30:00",
     *              "updated_at": "2025-01-15 10:30:00"
     *          }
     *      ]
     * }
     */
    public function index(WalletTransactionListRequest $request)
    {
        try {
            $user = $this->getCurrentUser();
            $data = $request->validated();

            $type = $data['type'] ?? null;
            $page = $data['page'] ?? 1;
            $limit = $data['limit'] ?? 10;
            $period = $data['period'] ?? null;
            $startDate = $data['start_date'] ?? null;
            $endDate = $data['end_date'] ?? null;

            $transactions = $this->repository->getByUserAndType(
                $user->id,
                $type,
                $page,
                $limit,
                $period,
                $startDate,
                $endDate
            );

            // Return items array for FE to handle loadmore
            return $this->jsonResponseSuccess(
                WalletTransactionResource::collection($transactions->items())
            );
        } catch (\Exception $e) {
            \Log::error('WalletTransactionController@index error: ' . $e->getMessage());
            return $this->jsonResponseError('Có lỗi xảy ra khi tải danh sách giao dịch: ' . $e->getMessage());
        }
    }

    /**
     * Nạp tiền vào ví
     *
     * Nạp tiền vào ví của user. Yêu cầu có ảnh chứng từ chuyển khoản.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam amount numeric required
     * Số tiền nạp (tối thiểu 10,000đ, tối đa 100,000,000đ). Example: 100000
     *
     * @bodyParam proof_image string required
     * Ảnh chứng từ chuyển khoản dạng base64 với data URI prefix. Example: data:image/jpeg;base64,/9j/4AAQSkZJRg...
     *
     * @bodyParam note string nullable
     * Ghi chú. Example: Nạp tiền vào ví
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Yêu cầu nạp tiền đã được gửi. Vui lòng chờ xác nhận.",
     *      "data": {
     *          "id": 1,
     *          "amount": 100000,
     *          "type": "deposit",
     *          "status": "pending",
     *          "note": "Nạp tiền vào ví",
     *          "order_id": null,
     *          "proof_image": "public/uploads/wallet/deposit_xxx.jpg",
     *          "created_at": "2025-01-30 10:30:00",
     *          "updated_at": "2025-01-30 10:30:00"
     *      }
     * }
     */
    public function deposit(DepositWalletRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getCurrentUser();
            $amount = $request->input('amount');
            $proofImage = $request->input('proof_image');
            $note = $request->input('note', 'Nạp tiền vào ví');

            // Upload proof image
            $proofImagePath = $this->fileService->uploadSingleFileBase64($proofImage);

            if (!$proofImagePath) {
                return $this->jsonResponseError(__('wallet_transaction.upload_proof_image_failed'), 400);
            }

            // Create wallet transaction
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => $amount,
                'type' => WalletTransactionType::Deposit->value,
                'status' => WalletTransactionStatus::Pending->value,
                'note' => $note,
                'proof_image' => $proofImagePath,
                'order_id' => null,
            ]);

            DB::commit();

            return $this->jsonResponseSuccess(
                new WalletTransactionResource($transaction),
                __('wallet_transaction.deposit_request_sent')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponseError(__('server_error_please_try_again'), 500);
        }
    }

    /**
     * Rút tiền khỏi ví
     *
     * Rút tiền từ ví của user. Số dư ví phải đủ để thực hiện giao dịch.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam amount numeric required
     * Số tiền rút (tối thiểu 50,000đ, tối đa 10,000,000đ). Example: 500000
     *
     * @bodyParam note string nullable
     * Ghi chú. Example: Rút tiền về tài khoản ngân hàng
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Yêu cầu rút tiền đã được gửi. Vui lòng chờ xác nhận.",
     *      "data": {
     *          "id": 2,
     *          "amount": -500000,
     *          "type": "withdraw",
     *          "status": "pending",
     *          "note": "Rút tiền về tài khoản ngân hàng",
     *          "order_id": null,
     *          "proof_image": null,
     *          "created_at": "2025-01-30 10:35:00",
     *          "updated_at": "2025-01-30 10:35:00"
     *      }
     * }
     */
    public function withdraw(WithdrawWalletRequest $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getCurrentUser();
            $amount = $request->input('amount');
            $note = $request->input('note', 'Rút tiền từ ví');

            // Check wallet balance
            if ($user->wallet_balance < $amount) {
                return $this->jsonResponseError('Số dư ví không đủ để thực hiện giao dịch này.', 400);
            }

            // Immediately deduct amount from wallet balance when creating withdraw request
            $user->decrement('wallet_balance', $amount);

            // Create wallet transaction with negative amount (for history / reporting)
            $transaction = WalletTransaction::create([
                'user_id' => $user->id,
                'amount' => -$amount, // Negative for withdraw
                'type' => WalletTransactionType::Withdraw->value,
                'status' => WalletTransactionStatus::Pending->value,
                'note' => $note,
                'proof_image' => null,
                'order_id' => null,
            ]);

            DB::commit();

            return $this->jsonResponseSuccess(
                new WalletTransactionResource($transaction),
                __('wallet_transaction.withdraw_request_sent')
            );
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->jsonResponseError(__('server_error_please_try_again'), 500);
        }
    }
}
