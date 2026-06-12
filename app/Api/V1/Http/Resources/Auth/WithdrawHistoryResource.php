<?php

namespace App\Api\V1\Http\Resources\Auth;

use App\Enums\WithdrawStatus;
use Illuminate\Http\Resources\Json\ResourceCollection;

class WithdrawHistoryResource extends ResourceCollection
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'user' => $item->user->fullname,
                'amount' => $item->amount,
                'status' => $item->status,
                'bank_account_number' => $item->user->bank_account_number,
                'bank_name' => $item->user->bank_name,
                'bank_account' => $item->user->bank_account,
                'processed_at' => $item->processed_at
            ];
        });
    }
}
