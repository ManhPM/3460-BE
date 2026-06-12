<?php

namespace App\Models;

use App\Enums\Contact\ContactStatus;
use App\Enums\DefaultStatus;
use App\Enums\Order\OrderStatus;
use App\Enums\Order\PaymentStatus;
use App\Enums\Payment\PaymentMethod;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'id',
        'payment_method',
        'note',
        'payment_image',
        'address',
        'email',
        'phone',
        'fullname',
        'discount_value',
        'discount_code',
        'total',
        'status',
        'payment_status',
        'code',
        'name_other',
        'address_other',
        'phone_other',
        'note_other',
        'user_id',
        'admin_id',
        'province_id',
        'ward_id',
        'points',
        'shipping_fee',
        'voucher_shipping_code',
        'voucher_shipping_discount_value',
        'voucher_product_code',
        'voucher_product_discount_value',
        'zalo_order_id',
        'shipping_date',
        'cancel_reason',
        'is_deleted',
        'points_discount_value',
        'points_earned',
        'member_ship_points_earned',
        'membership_discount_percentage',
        'membership_discount_value',
        'bank_id'
    ];

    protected $casts = [
        'total' => 'double',
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'payment_method' => PaymentMethod::class,
        'bank_id' => 'integer',
    ];


    public function details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'order_id')->orderBy('id', 'desc');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contact(): HasOne
    {
        return $this->hasOne(Contact::class)->where('status', ContactStatus::Approved);
    }

    public function scopeCurrentAuth($query)
    {
        return $query->where('user_id', auth()->user()->id);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'admin_id');
    }
}
