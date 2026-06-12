<?php

namespace App\Api\V1\Http\Resources\Auth;

use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class AuthResource extends JsonResource
{
    public function toArray($request)
    {
        $data =  [
            'member' => $this->member,
            'id' => $this->id,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'points' => $this->points,
            'membership_level_points' => $this->membership_level_points,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_account_number' => $this->bank_account_number,
            'affiliate_code' => $this->affiliate_code,
            'wallet_balance' => $this->wallet_balance,
            'monthly_savings' => $this->monthly_savings,
        ];
        return $data;
    }
}
