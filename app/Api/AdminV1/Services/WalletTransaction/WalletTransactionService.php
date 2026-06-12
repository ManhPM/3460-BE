<?php

namespace App\Api\AdminV1\Services\WalletTransaction;

use App\Api\AdminV1\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use App\Models\User;
use App\Traits\SendNotification;

class WalletTransactionService
{
    use SendNotification;

    protected $repository;

    public function __construct(WalletTransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function create(array $data)
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data)
    {
        return $this->repository->update($id, $data);
    }

    public function delete(int $id)
    {
        return $this->repository->delete($id);
    }

    public function approve(int $id)
    {
        $instance = $this->repository->findOrFail($id);
        if ($instance->status !== WalletTransactionStatus::Pending->value) {
            return false;
        }
        $payload = ['status' => WalletTransactionStatus::Approved->value];
        $updated = $this->repository->update($id, $payload);
        if ($updated) {
            // Load user with device_token for notification
            $user = User::find($instance->user_id);

            if (in_array($instance->type, [WalletTransactionType::Deposit->value, WalletTransactionType::Refund->value])) {
                User::where('id', $instance->user_id)->increment('wallet_balance', $instance->amount);

                // Send notification for deposit/refund transactions
                if ($user && $instance->type === WalletTransactionType::Deposit->value) {
                    $amountFormatted = number_format($instance->amount, 0, ',', '.') . ' ' . config('custom.currency');
                    $title = 'Nạp tiền thành công';
                    $message = "Giao dịch nạp tiền của bạn đã được duyệt. Số tiền {$amountFormatted} đã được cộng vào ví của bạn.";
                    $this->sendNotification(
                        $user->id,
                        $title,
                        $message,
                        $user->device_token
                    );
                }
            }
        }
        return $updated;
    }

    public function reject(int $id)
    {
        $instance = $this->repository->findOrFail($id);
        if ($instance->status !== WalletTransactionStatus::Pending->value) {
            return false;
        }
        $payload = ['status' => WalletTransactionStatus::Rejected->value];
        $updated = $this->repository->update($id, $payload);

        // If this is a withdraw transaction, refund money back to wallet on reject
        if ($updated && $instance->type === WalletTransactionType::Withdraw->value) {
            $refundAmount = abs($instance->amount); // amount is stored as negative for withdraw
            User::where('id', $instance->user_id)->increment('wallet_balance', $refundAmount);
        }

        return $updated;
    }
}
