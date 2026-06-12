<?php

namespace App\Models;

use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VoucherProgram extends Model
{
    use HasFactory;

    protected $table = 'voucher_programs';
    protected $fillable = [
        'name',
        'expiration_days',
        'min_order_amount',
        'max_discount_value',
        'discount_value',
        'type',
        'voucher_type',
        'avatar',
        'qty',
        'status'
    ];

    protected $casts = [
        'type' => DiscountValueType::class,
        'voucher_type' => VoucherType::class,
    ];

    public function user_voucher_logs(): HasMany
    {
        return $this->hasMany(UserVoucherLog::class, 'voucher_program_id', 'id');
    }
}
