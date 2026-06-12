<?php

namespace App\Models;

use App\Enums\Discount\DiscountValueType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Discount extends Model
{
    use HasFactory;

    protected $table = 'discounts';
    protected $dates = ['date_start', 'date_end'];
    protected $fillable = [
        'code',
        'date_start',
        'date_end',
        'max_usage',
        'min_order_amount',
        'type',
        'discount_value',
        'max_discount_value',
        'max_usage_per_user',
    ];

    protected $casts = [
        'type' => DiscountValueType::class,
    ];

    public function isActive(): bool
    {
        $now = Carbon::now();

        if ($now->greaterThan($this->date_start) && $now->lessThan($this->date_end)) {
            if ($this->max_usage !== null && $this->max_usage <= 0) {
                return false;
            }
            return true;
        }

        return false;
    }

    public function scopeActive($query)
    {
        $now = Carbon::now()->toDateTimeString();
        return $query->where('status', '=', 1)
            ->where('date_start', '<=', $now)
            ->where('date_end', '>=', $now);
    }
}
