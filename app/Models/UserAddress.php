<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    use HasFactory;

    protected $table = 'user_addresses';
    protected $fillable = [
        'name',
        'fullname',
        'phone',
        'address',
        'email',
        'province_id',
        'ward_id',
        'is_default',
        'user_id'
    ];

    protected $casts = [
        'is_default' => 'integer',
        'user_id' => 'integer',
        'province_id' => 'integer',
        'ward_id' => 'integer',
    ];

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_id');
    }

    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class, 'ward_id');
    }
}
