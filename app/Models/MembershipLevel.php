<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'min_points',
        'discount_percentage',
        'shipping_discount_amount',
        'color_1',
        'color_2',
        'color_3',
        'icon',
        'description'
    ];

    protected $casts = [
        'min_points' => 'integer',
        'discount_percentage' => 'integer',
        'shipping_discount_amount' => 'integer',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'membership_id', 'id');
    }
}
