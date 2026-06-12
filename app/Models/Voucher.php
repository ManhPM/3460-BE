<?php

namespace App\Models;

use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';
    protected $dates = ['date_end'];
    protected $fillable = [
        'user_id',
        'code',
        'date_end',
        'is_used',
        'min_order_amount',
        'type',
        'voucher_type',
        'discount_value',
        'max_discount_value',
        'avatar',
    ];

    protected $casts = [
        'type' => DiscountValueType::class,
        'voucher_type' => VoucherType::class,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
