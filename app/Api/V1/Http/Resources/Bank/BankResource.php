<?php

namespace App\Api\V1\Http\Resources\Bank;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BankResource extends ResourceCollection
{
    public function toArray($request)
    {
        return $this->collection->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name ?? '',
                'code' => $item->code ?? '',
                'short_name' => $item->short_name ?? '',
                'logo' => $item->logo ?? '',
                'bank_account' => $item->bank_account ?? '',
                'bank_account_number' => $item->bank_account_number ?? '',
            ];
        });
    }
}
