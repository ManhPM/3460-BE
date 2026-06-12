<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductAttributeVariation extends Model
{
    use HasFactory;

    protected $table = 'products_attributes_variations';

    protected $guarded = [];

    public $timestamps = false;
}
