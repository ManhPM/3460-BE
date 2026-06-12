<?php

namespace App\Api\AdminV1\Http\Requests\MembershipLevel;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexMembershipLevelRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}

