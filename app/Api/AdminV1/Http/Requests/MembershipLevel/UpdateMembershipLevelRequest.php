<?php

namespace App\Api\AdminV1\Http\Requests\MembershipLevel;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateMembershipLevelRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'min_points' => ['sometimes', 'required', 'integer', 'min:0'],
            'discount_percentage' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'color_1' => ['nullable', 'string', 'max:7'],
            'color_2' => ['nullable', 'string', 'max:7'],
            'color_3' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ];
    }
}

