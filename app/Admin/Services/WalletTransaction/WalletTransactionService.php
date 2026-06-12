<?php

namespace App\Admin\Services\WalletTransaction;

use App\Admin\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Enums\Transaction\WalletTransactionStatus;
use App\Enums\Transaction\WalletTransactionType;
use Illuminate\Http\Request;
use App\Models\User;
use App\Traits\SendNotification;

class WalletTransactionService implements WalletTransactionServiceInterface
{
    use SendNotification;

    protected $data;
    protected $repository;

    public function __construct(WalletTransactionRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        return $this->repository->create($this->data);
    }

    public function update(Request $request)
    {
        $this->data = $request->validated();
        return $this->repository->update($this->data['id'], $this->data);
    }

    public function delete($id)
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
