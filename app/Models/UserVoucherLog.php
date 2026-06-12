<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserVoucherLog extends Model
{
    use HasFactory;

    protected $table = 'user_voucher_logs';
    protected $fillable = [
        'user_id',
        'voucher_program_id',
    ];
}
