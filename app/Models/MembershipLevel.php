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
        'color_1',
        'color_2',
        'color_3',
        'icon',
        'description'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'membership_id', 'id');
    }
}
