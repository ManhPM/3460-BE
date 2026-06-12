<?php

namespace App\Admin\Http\Requests\PostCategory;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\PostCategory\PostCategoryStatus;
use BenSampo\Enum\Rules\EnumValue;
use App\Admin\Rules\Category\CategoryParent;

class PostCategoryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string'],
            'desc' => ['required', 'string'],
            'avatar' => ['required', 'string'],
            'parent_id' => ['nullable', 'exists:App\Models\PostCategory,id'],
            'position' => ['nullable', 'integer'],
            'is_home' => ['required'],
            'status' => ['required', new EnumValue(PostCategoryStatus::class, false)]
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\PostCategory,id'],
            'desc' => ['required', 'string'],
            'name' => ['required', 'string'],
            'slug' => ['required'],
            'parent_id' => ['nullable', 'exists:App\Models\PostCategory,id', new CategoryParent($this->id)],
            'position' => ['nullable', 'integer'],
            'avatar' => ['required', 'string'],
            'is_home' => ['required'],
            'status' => ['required', new EnumValue(PostCategoryStatus::class, false)]
        ];
    }
    public function messages()
    {
        return [
            'id.required' => 'ID danh mục là bắt buộc.',
            'id.exists' => 'Danh mục không tồn tại.',
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là chuỗi.',
            'desc.required' => 'Mô tả danh mục là bắt buộc.',
            'desc.string' => 'Mô tả danh mục phải là chuỗi.',
            'avatar.required' => 'Ảnh đại diện danh mục là bắt buộc.',
            'avatar.string' => 'Ảnh đại diện phải là chuỗi.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
            'position.integer' => 'Vị trí phải là số nguyên.',
            'is_home.required' => 'Trường is_home là bắt buộc.',
            'status.required' => 'Trạng thái danh mục là bắt buộc.',
            'status.enum_value' => 'Trạng thái danh mục không hợp lệ.',
            'slug.required' => 'Slug danh mục là bắt buộc.',
        ];
    }
}
