<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductVariationVariation extends Model
{
    use HasFactory;

    protected $table = 'products_variations_variations';

    protected $guarded = [];

    public $timestamps = false;
}
