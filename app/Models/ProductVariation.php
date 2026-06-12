<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariation extends Model
{
    use HasFactory;

    protected $table = 'products_variations';

    protected $guarded = [];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function attribute_variations()
    {
        return $this->belongsToMany(AttributeVariation::class, 'products_variations_variations', 'product_variation_id', 'attribute_variation_id')->orderBy('position', 'asc');
    }

    public function attributeVariations()
    {
        return $this->belongsToMany(AttributeVariation::class, 'products_variations_variations', 'product_variation_id', 'attribute_variation_id')->orderBy('position', 'asc');
    }

    public function adminInventories()
    {
        return $this->hasMany(AdminInventory::class, 'product_variation_id');
    }

    public function admin_inventories()
    {
        return $this->hasMany(AdminInventory::class, 'product_variation_id');
    }

    public function getFlashsalePriceAttribute()
    {
        $product = $this->product;
        if (!$product || !$product->is_flash_sale) {
            return null;
        }

        $detail = $product->is_flash_sale
            ->details()
            ->where('product_variation_id', $this->id)
            ->first();

        return $detail ? $detail->flashsale_price : null;
    }
}
