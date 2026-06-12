<?php

namespace App\Api\AdminV1\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->fullname, // Frontend dùng 'name' nhưng giá trị từ 'fullname'
            'fullname' => $this->fullname,
            'email' => $this->email,
            'phone' => $this->phone,
            'avatar' => $this->avatar ? asset($this->avatar) : null,
            'address' => $this->address,
            'gender' => $this->gender?->value,
            'birthday' => $this->birthday,
            'points' => $this->points ?? 0,
            'commission' => $this->commission ?? 0,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'is_email_verified' => $this->is_email_verified ?? ($this->email_verified_at ? 1 : 0),
            'is_phone_verified' => $this->is_phone_verified ?? 0,
            'membership_level' => $this->whenLoaded('member', function () {
                return [
                    'id' => $this->member->id,
                    'name' => $this->member->name,
                ];
            }),
            'membership_id' => $this->membership_id,
            'membership_level_points' => $this->membership_level_points ?? 0,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_account_number' => $this->bank_account_number,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
