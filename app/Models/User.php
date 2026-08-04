<?php

namespace App\Models;

use App\Enums\Contact\ContactStatus;
use App\Enums\Contact\ContactType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use App\Enums\User\{Gender};
use App\Enums\Voucher\VoucherType;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements JWTSubject
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $columnSlug = 'fullname';

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'remember_token',
    ];

    protected static function booted()
    {
        static::creating(function ($user) {
            if (empty($user->avatar)) {
                $user->avatar = 'public/assets/images/default-avatar.png'; // Giá trị mặc định cho avatar
            }
        });
    }

    protected $fillable = [
        'code',
        'fullname',
        'email',
        'phone',
        'address',
        'avatar',
        'bank_account',
        'affiliate_code',
        'referrer_code',
        'bank_name',
        'bank_account_number',
        'points',
        'commission',
        'verify_code',
        'verify_code_expiration',
        'birthday',
        'device_token',
        'gender',
        'email_verified_at',
        'token_get_password',
        'password',
        'is_email_verified',
        'membership_id',
        'membership_level_points',
        'is_phone_verified',
        'wallet_balance'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'gender' => Gender::class,
    ];


    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'model_has_roles', 'model_id', 'role_id')
            ->withPivot('model_type')
            ->wherePivot('model_type', self::class);
    }


    public function checkPermissions($permissionsArr): bool
    {
        foreach ($permissionsArr as $permission) {
            if ($this->can($permission)) {
                return true;
            }
        }
        return false;
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'user_id', 'id');
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class, 'user_id', 'id');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'id')
            ->orderByRaw('read_at IS NOT NULL, id DESC');
    }


    public function vouchers(): HasMany
    {
        return $this->hasMany(Voucher::class, 'user_id', 'id');
    }

    public function discounts(): HasMany
    {
        return $this->hasMany(Discount::class, 'user_id', 'id');
    }

    public function shopping_cart(): HasMany
    {
        return $this->hasMany(ShoppingCart::class, 'user_id', 'id');
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(MembershipLevel::class, 'membership_id');
    }


    public function addresses(): HasMany
    {
        return $this->hasMany(UserAddress::class, 'user_id', 'id')->orderBy('is_default', 'desc')->with('province', 'ward');
    }

    public function unreadNotifications(): HasMany
    {
        return $this->hasMany(Notification::class, 'user_id', 'id')->where('read_at', null)->orderBy('id', 'desc');
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims(): array
    {
        return [];
    }

    public function getShippingVoucherAttribute()
    {
        return $this->vouchers
            ->where('voucher_type', VoucherType::Shipping)
            ->where('date_end', '>', now())
            ->where('is_used', 0)
            ->values() // Lấy mảng tuần tự
            ->all();  // Chuyển đổi thành mảng
    }

    public function getProductVoucherAttribute()
    {
        return $this->vouchers
            ->where('voucher_type', VoucherType::Product)
            ->where('date_end', '>', now())
            ->where('is_used', 0)
            ->values() // Lấy mảng tuần tự
            ->all();  // Chuyển đổi thành mảng
    }

    /**
     * Tính tổng số tiền tiết kiệm (giảm giá) của người dùng trong tháng hiện tại
     * từ các đơn hàng không bị hủy.
     *
     * @return float
     */
    public function getMonthlySavingsAttribute()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        return (float) \App\Models\Order::where('user_id', $this->id)
            ->where('status', '!=', \App\Enums\Order\OrderStatus::Cancelled->value)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->selectRaw('SUM(
                COALESCE(discount_value, 0) + 
                COALESCE(voucher_shipping_discount_value, 0) + 
                COALESCE(voucher_product_discount_value, 0) + 
                COALESCE(points_discount_value, 0) + 
                COALESCE(membership_discount_value, 0) +
                COALESCE(membership_shipping_discount_value, 0)
            ) as total_savings')
            ->value('total_savings') ?? 0.0;
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(User::class, 'referrer_code', 'affiliate_code');
    }
}

