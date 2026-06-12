<?php

namespace App\Api\AdminV1\Http\Requests\Post;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class IndexPostRequest extends BaseRequest
{
    protected function methodGet(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string'],
            'category_id' => ['nullable', 'exists:posts_categories,id'],
            'per_page' => ['nullable', 'integer', 'min:1'],
        ];
    }
}
