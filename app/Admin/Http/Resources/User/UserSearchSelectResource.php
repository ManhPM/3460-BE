<?php

namespace App\Admin\Http\Resources\User;

use Illuminate\Http\Resources\Json\JsonResource;

class UserSearchSelectResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' =>  $this->fullname .
                ' | ' . ($this->phone ?: 'Không có') .
                ' | ' . ($this->email ?: 'Không có')
        ];
    }
}
