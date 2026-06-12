<?php

namespace App\Api\AdminV1\Http\Requests\Post;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StorePostRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:posts,slug'],
            'content' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'status' => ['nullable', 'string'],
            'is_featured' => ['nullable', 'boolean'],
            'category_ids' => ['nullable', 'array'],
            'category_ids.*' => ['exists:posts_categories,id'],
        ];
    }
}
