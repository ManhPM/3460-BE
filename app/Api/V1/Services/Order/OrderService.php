<?php

namespace App\Api\V1\Services\Order;

use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Api\V1\Services\Order\OrderServiceInterface;
use App\Api\V1\Repositories\Order\{OrderRepositoryInterface};
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;
use App\Api\V1\Support\AuthSupport;
use App\Enums\Order\OrderStatus;
use App\Enums\Payment\PaymentMethod;
use App\Traits\SendNotification;
use App\Traits\UseLog;
use Exception;
use Illuminate\Http\Request;

class OrderService implements OrderServiceInterface
{
    use AuthSupport, UseLog, SendNotification;

    protected $data;
    protected $fileService;

    protected $repository;
    protected $userRepository;
    protected $voucherRepository;
    protected $discountRepository;
    protected $settingRepository;
    protected $bankRepository;

    public function __construct(
        OrderRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        VoucherRepositoryInterface $voucherRepository,
        DiscountRepositoryInterface $discountRepository,
        SettingRepositoryInterface $settingRepository,
        BankRepositoryInterface $bankRepository,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->voucherRepository = $voucherRepository;
        $this->discountRepository = $discountRepository;
        $this->settingRepository = $settingRepository;
        $this->bankRepository = $bankRepository;
        $this->fileService = $fileService;
    }

    public function cancel(Request $request)
    {
        $data = $request->validated();

        DB::beginTransaction();
        try {
            $order = $this->repository->find($data['id']);

            if (!$order || in_array($order->status, [OrderStatus::Cancelled, OrderStatus::Completed])) {
                return false;
            }

            $this->restoreDiscountUsage($order);
            $this->restoreUserPoints($order);
            $this->restoreVouchers($order);
            $this->repository->update($data['id'], ['status' => OrderStatus::Cancelled]);
            $this->refundWalletIfNeeded($order);

            DB::commit();
            return true;
        } catch (Exception $e) {
            $this->logError('Failed to cancel order: ', $e);
            DB::rollBack();
            return false;
        }
    }

    private function restoreDiscountUsage($order)
    {
        if (!$order->discount_code) {
            return;
        }

        $discount = $this->discountRepository->findByField('code', $order->discount_code);
        if ($discount) {
            $discount->increment('max_usage');
        }
    }

    private function restoreUserPoints($order)
    {
        if ($order->points > 0) {
            $this->userRepository->update(
                $order->user_id,
                ['points' => $order->user->points + $order->points]
            );
        }
    }

    private function restoreVouchers($order)
    {
        if ($order->voucher_shipping_code) {
            $voucherShipping = $this->voucherRepository->findByField('code', $order->voucher_shipping_code);
            $voucherShipping?->update(['is_used' => 0]);
        }

        if ($order->voucher_product_code) {
            $voucherProduct = $this->voucherRepository->findByField('code', $order->voucher_product_code);
            $voucherProduct?->update(['is_used' => 0]);
        }
    }

    private function refundWalletIfNeeded($order)
    {
        if ($order->payment_method != PaymentMethod::Wallet || !$order->user) {
            return;
        }

        $order->refresh();
        $refundable = ($order->total + ($order->shipping_fee ?? 0))
            - (($order->discount_value ?? 0) + ($order->voucher_shipping_discount_value ?? 0) + ($order->voucher_product_discount_value ?? 0) + ($order->membership_discount_value ?? 0) + ($order->membership_shipping_discount_value ?? 0));

        $user = $order->user;
        $this->userRepository->update($user->id, ['wallet_balance' => $user->wallet_balance + $refundable]);

        \App\Models\WalletTransaction::create([
            'user_id' => $user->id,
            'amount' => $refundable,
            'type' => 'refund',
            'status' => 'approved',
            'order_id' => $order->id,
            'note' => 'Hoàn tiền vào ví do huỷ đơn',
        ]);
    }

    public function getBankTransferInfo($orderId)
    {
        $order = $this->repository->findOrFail($orderId);

        $bank = $this->bankRepository->find($order->bank_id);

        // Tính tổng tiền cần thanh toán
        $orderCode = $order->code;
        $totalAmount = $order->total -
            ($order->discount_value ?? 0) -
            ($order->points_discount_value ?? 0) +
            ($order->shipping_fee ?? 0) -
            ($order->voucher_product_discount_value ?? 0) -
            ($order->voucher_shipping_discount_value ?? 0) -
            ($order->membership_discount_value ?? 0) -
            ($order->membership_shipping_discount_value ?? 0);

        // Tạo mã QR dựa trên order_id
        $bankCode = $bank->code;
        $accountNumber = $bank->bank_account_number;
        $accountName = urlencode($bank->bank_account);
        $qrImageUrl = "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-print.png?amount={$totalAmount}&addInfo=THANH%20TOAN%20DON%20HANG%20{$orderCode}&accountName={$accountName}";

        return [
            'qr_image_url' => $qrImageUrl,
        ];
    }

    /**
     * Generate QR code URL for bank transfer (internal use, no validation)
     */
    public function generateQrImageUrl($order)
    {
        if ($order->payment_method != PaymentMethod::Banking->value || !$order->bank_id) {
            return null;
        }

        $bank = $this->bankRepository->find($order->bank_id);
        if (!$bank) {
            return null;
        }

        $orderCode = $order->code;
        $totalAmount = $order->total -
            ($order->discount_value ?? 0) -
            ($order->points_discount_value ?? 0) +
            ($order->shipping_fee ?? 0) -
            ($order->voucher_product_discount_value ?? 0) -
            ($order->voucher_shipping_discount_value ?? 0) -
            ($order->membership_discount_value ?? 0) -
            ($order->membership_shipping_discount_value ?? 0);

        $bankCode = $bank->code;
        $accountNumber = $bank->bank_account_number;
        $accountName = urlencode($bank->bank_account);

        return "https://img.vietqr.io/image/{$bankCode}-{$accountNumber}-print.png?amount={$totalAmount}&addInfo=THANH%20TOAN%20DON%20HANG%20{$orderCode}&accountName={$accountName}";
    }

    public function updateOrder($orderId, array $data)
    {
        $order = $this->repository->findOrFail($orderId);

        $updateData = [];

        // Update bank_id if provided
        if (isset($data['bank_id'])) {
            $updateData['bank_id'] = $data['bank_id'];
        }

        // Update payment_image if provided
        if (isset($data['payment_image']) && !empty($data['payment_image'])) {
            $paymentImage = $this->fileService->uploadSingleFileBase64($data['payment_image']);
            $updateData['payment_image'] = $paymentImage;
            $updateData['payment_status'] = \App\Enums\Order\PaymentStatus::Pending->value;
        }

        if (!empty($updateData)) {
            $order->update($updateData);
        }

        return [
            'id' => $order->id,
            'bank_id' => $order->bank_id,
            'payment_image' => $order->payment_image,
        ];
    }
}
