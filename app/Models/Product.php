<?php

namespace App\Models;

use App\Enums\DefaultActiveStatus;
use App\Supports\Eloquent\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use App\Enums\Product\ProductType;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

class Product extends Model
{
    use HasFactory, Sluggable;

    protected $table = 'products';

    protected $fillable = [
        'id',
        'name', // Tên sản phẩm
        'slug',
        'price', // Giá sản phẩm
        'promotion_price', // Giá khuyến mãi
        'qty', // Số lượng
        'type', // Loại sản phẩm
        'is_active', // Hoạt động
        'avatar', // Ảnh
        'gallery', // Bộ sưu tập ảnh
        'desc', // Mô tả
        'is_featured', // Nổi bật
        'is_contact_price', // Giá liên hệ,
        'is_deleted', // Xóa
    ];

    protected $columnSlug = 'name';


    protected static function boot()
    {
        parent::boot();
    }

    protected $casts = [
        'gallery' => AsArrayObject::class,
        'type' => ProductType::class,
        'is_featured' => DefaultActiveStatus::class,
        'price' => 'double',
        'promotion_price' => 'double'
    ];

    public function getFlashsalePriceAttribute()
    {
        $now = Carbon::now();

        $flashSale = $this->flash_sales()
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->where('is_active', 1)
            ->whereRaw('qty > sold')
            ->first();

        return $flashSale ? $flashSale->pivot->flashsale_price : null;
    }

    public function getStarCount($column, $rating)
    {
        return $this->reviews()->where($column, $rating)->count();
    }

    public function getRatingsAttribute()
    {
        return collect(range(1, 5))->mapWithKeys(fn($star) => [$star => $this->getStarCount('rating', $star)]);
    }
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'products_categories', 'product_id', 'category_id')->orderBy('position', 'asc');
    }
    public function attributes(): BelongsToMany
    {
        return $this->belongsToMany(Attribute::class, ProductAttribute::class, 'product_id', 'attribute_id')->orderBy('position', 'asc');
    }
    public function productAttributes(): HasMany
    {
        return $this->hasMany(ProductAttribute::class, 'product_id')->orderBy('position', 'asc');
    }

    public function productVariations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id')->orderBy('position', 'asc');
    }

    public function product_variations(): HasMany
    {
        return $this->hasMany(ProductVariation::class, 'product_id')->orderBy('position', 'asc');
    }

    public function getMinPromotionPriceAttribute()
    {
        return $this->productVariations->min('promotion_price');
    }
    public function getMaxPromotionPriceAttribute()
    {
        return $this->productVariations->max('promotion_price');
    }

    public function getTotalQtyAttribute()
    {
        return $this->productVariations->sum('qty');
    }

    public function productVariation(): HasOne
    {
        return $this->hasOne(ProductVariation::class, 'product_id');
    }

    public function product_variation(): HasOne
    {
        return $this->hasOne(ProductVariation::class, 'product_id');
    }

    public function adminInventories(): HasMany
    {
        return $this->hasMany(AdminInventory::class, 'product_id');
    }

    public function admin_inventories(): HasMany
    {
        return $this->hasMany(AdminInventory::class, 'product_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
    public function scopeSimple($query)
    {
        return $query->where('type', ProductType::Simple);
    }
    public function scopeVariable($query)
    {
        return $query->where('type', ProductType::Variable);
    }
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'product_id', 'id');
    }

    public function flash_sales(): BelongsToMany
    {
        return $this->belongsToMany(FlashSale::class, 'flash_sales_products', 'product_id', 'flash_sale_id')
            ->withPivot('flashsale_price');
    }

    public function getIsFlashSaleAttribute()
    {
        $now = Carbon::now();

        return $this->flash_sales()
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->where('is_active', 1)
            ->whereRaw('qty > sold')
            ->first();
    }

    public function order_details(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'product_id', 'id');
    }

    public function getAvgRatingAttribute()
    {
        return $this->reviews->avg('rating') ?? 0;
    }
    public function getTotalSoldAttribute()
    {
        return $this->order_details->count();
    }

    public function isSimple()
    {
        if ($this->type == ProductType::Simple) {
            return true;
        }
        return false;
    }
}
