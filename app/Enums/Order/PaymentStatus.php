<?php

namespace App\Enums\Order;

use App\Supports\Enum;
use Illuminate\Validation\ValidationException;

enum PaymentStatus: string
{
    use Enum;

    case Paid = 'paid';
    case Unpaid = 'unpaid';
    case Pending = 'pending';

    public function label(): string
    {
        return match ($this) {
            self::Paid => 'Đã thanh toán',
            self::Unpaid => 'Chưa thanh toán',
            self::Pending => 'Thanh toán một phần',
        };
    }

    public function badge(): string
    {
        return match ($this) {
            self::Paid => 'bg-green',
            self::Unpaid => 'bg-orange',
            self::Pending => 'bg-info',
        };
    }

    /**
     * Lấy danh sách trạng thái thanh toán được phép chuyển đến từ trạng thái hiện tại
     */
    public function getAllowedTransitions(): array
    {
        return match ($this) {
            self::Unpaid => [self::Paid, self::Pending], // Chưa thanh toán có thể chuyển sang đã thanh toán hoặc thanh toán một phần
            self::Pending => [self::Paid], // Thanh toán một phần có thể chuyển sang đã thanh toán
            self::Paid => [self::Paid],
        };
    }

    /**
     * Kiểm tra xem có thể chuyển sang trạng thái thanh toán mới hay không
     */
    public function canTransitionTo(PaymentStatus $newStatus): bool
    {
        return in_array($newStatus, $this->getAllowedTransitions());
    }

    /**
     * Validate và throw exception nếu chuyển đổi không hợp lệ
     */
    public function validateTransition(PaymentStatus $newStatus): void
    {
        if (!$this->canTransitionTo($newStatus)) {
            $message = '';

            // Nếu đã thanh toán thì không thể chuyển về chưa thanh toán
            if ($this === self::Paid && $newStatus === self::Unpaid) {
                $message = 'Không thể chuyển trạng thái thanh toán từ [Đã thanh toán] về [Chưa thanh toán].';
            }
            // Trường hợp chung
            else {
                $message = sprintf(
                    'Không thể chuyển trạng thái thanh toán từ [%s] sang [%s].',
                    $this->description(),
                    $newStatus->description()
                );
            }

            throw ValidationException::withMessages([
                'payment_status' => [$message]
            ]);
        }
    }
}
