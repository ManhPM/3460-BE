<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashSale extends Model
{
    use HasFactory;

    // Tắt quản lý thời gian tạo và cập nhật
    public $timestamps = false;

    protected $table = 'flash_sales';
    protected $fillable = [
        'id',
        /** Tên chương trình flash sale */
        'name',
        /** Thời gian bắt đầu */
        'start_time',
        /** Thời gian kết thúc */
        'end_time',
        /** Trạng thái */
        'is_active',
    ];

    protected $casts = [];

    public function details(): HasMany
    {
        return $this->hasMany(FlashSaleDetail::class, 'flash_sale_id')->orderBy('product_id', 'desc');
    }
}
