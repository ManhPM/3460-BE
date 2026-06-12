<?php

namespace App\Models;

use App\Enums\Notification\NotificationStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends  Model
{
    use HasFactory;
    protected $table = 'notifications';

    protected $fillable = [
        /** user_id */
        'user_id',
        /** admin_id */
        'admin_id',
        /** Tiêu đề thông báo */
        'title',
        /** Nội dung ngắn */
        'short_message',
        /** Nội dung thông báo */
        'message',
        /** Trạng thái thông báo 1: Chưa đọc, 2: Đã đọc */
        'status',
        /** Thời gian đọc */
        'read_at',
        'avatar'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    protected $casts = [
        'status' => NotificationStatus::class,
    ];
    public function markAsRead(): void
    {
        $status = $this->status;

        if ($status === NotificationStatus::NOT_READ) {
            $this->status = NotificationStatus::READ;
            $this->read_at = now();
            $this->save();
        }
    }
}
