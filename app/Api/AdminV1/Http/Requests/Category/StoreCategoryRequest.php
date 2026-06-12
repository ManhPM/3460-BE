<?php

namespace App\Api\AdminV1\Http\Requests\Category;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class StoreCategoryRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:categories,slug'],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive'],
            'order' => ['nullable', 'integer'],
        ];
    }
}
