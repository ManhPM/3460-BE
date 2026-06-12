<?php

namespace App\Api\AdminV1\Http\Requests\Category;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class UpdateCategoryRequest extends BaseRequest
{
    protected function methodPut(): array
    {
        $categoryId = $this->route('category');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'unique:categories,slug,' . $categoryId],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'status' => ['sometimes', 'required', 'in:active,inactive'],
            'order' => ['nullable', 'integer'],
        ];
    }
}
