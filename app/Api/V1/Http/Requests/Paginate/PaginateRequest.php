<?php

namespace App\Api\V1\Http\Requests\Paginate;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Enums\Notification\NotificationStatus;
use Illuminate\Validation\Rules\Enum;

class PaginateRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet(): array
    {
        return [

            'page' => ['nullable'],
            'limit' => ['nullable'],
            'status' => ['nullable', new Enum(NotificationStatus::class)],
        ];
    }
}
