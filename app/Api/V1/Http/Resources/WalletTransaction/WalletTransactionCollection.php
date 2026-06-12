<?php

namespace App\Api\V1\Http\Resources\WalletTransaction;

use Illuminate\Http\Resources\Json\ResourceCollection;

class WalletTransactionCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return [
            'data' => new WalletTransactionResource($this->collection),
        ];
    }
}
