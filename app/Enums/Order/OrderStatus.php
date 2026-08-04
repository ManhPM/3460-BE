<?php

namespace App\Enums\Order;

use App\Supports\Enum;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

enum OrderStatus: string
{
    use Enum;

    case Pending = 'pending';
    case Confirmed = 'confirmed';
    case Delivering = 'delivering';
    case Completed = 'completed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'Chờ xác nhận',
            self::Confirmed => 'Đã xác nhận',
            self::Delivering => 'Đang giao hàng',
            self::Completed => 'Hoàn thành',
            self::Cancelled => 'Hủy bỏ',
        };
    }
    public function badge(): string
    {
        return match ($this) {
            self::Pending => 'bg-orange',
            self::Confirmed => 'bg-blue',
            self::Delivering => 'bg-pink',
            self::Completed => 'bg-green',
            self::Cancelled => 'bg-red',
        };
    }

    /**
     * Lấy danh sách trạng thái được phép chuyển đến từ trạng thái hiện tại
     */
    public function getAllowedTransitions(): array
    {
        return match ($this) {
            self::Pending => [self::Confirmed, self::Delivering, self::Completed, self::Cancelled],
            self::Confirmed => [self::Delivering, self::Completed, self::Cancelled],
            self::Delivering => [self::Completed, self::Cancelled],
            self::Completed => [], // Đơn hàng hoàn thành không thể chuyển sang trạng thái khác
            self::Cancelled => [], // Đơn hàng đã hủy không thể chuyển sang trạng thái khác
        };
    }

    /**
     * Kiểm tra xem có thể chuyển sang trạng thái mới hay không
     */
    public function canTransitionTo(OrderStatus $newStatus): bool
    {
        return in_array($newStatus, $this->getAllowedTransitions());
    }

    /**
     * Validate và throw exception nếu chuyển đổi không hợp lệ
     */
    public function validateTransition(OrderStatus $newStatus): void
    {
        if (!$this->canTransitionTo($newStatus)) {
            $message = '';

            // Nếu đơn hàng đã hoàn thành
            if ($this === self::Completed) {
                $message = 'Không thể thay đổi trạng thái của đơn hàng đã hoàn thành.';
            }
            // Nếu đơn hàng đã hủy
            elseif ($this === self::Cancelled) {
                $message = 'Không thể thay đổi trạng thái của đơn hàng đã hủy.';
            }
            // Trường hợp chung - chỉ báo lỗi dựa trên getAllowedTransitions
            else {
                $allowedStatuses = array_map(
                    fn($status) => $status->description(),
                    $this->getAllowedTransitions()
                );

                if (empty($allowedStatuses)) {
                    $message = sprintf(
                        'Không thể thay đổi trạng thái từ [%s].',
                        $this->description()
                    );
                } else {
                    $message = sprintf(
                        'Không thể chuyển từ trạng thái [%s] sang [%s]. Chỉ có thể chuyển sang: %s.',
                        $this->description(),
                        $newStatus->description(),
                        implode(', ', $allowedStatuses)
                    );
                }
            }

            throw ValidationException::withMessages([
                'status' => [$message]
            ]);
        }
    }
}
