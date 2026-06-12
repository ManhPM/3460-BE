<?php

namespace App\Api\AdminV1\Http\Requests\PostCategory;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
            'id.required' => __('please_enter_category_id'),
            'id.exists' => __('category_id_not_exists'),
            'name.required' => __('please_enter_category_name'),
            'name.string' => __('category_name_string'),
            'desc.required' => __('please_enter_category_desc'),
            'desc.string' => __('category_desc_string'),
            'avatar.required' => __('please_enter_category_avatar'),
            'avatar.string' => __('category_avatar_string'),
            'parent_id.exists' => __('category_parent_id_invalid'),
            'position.integer' => __('category_position_integer'),
            'is_home.required' => __('please_choose_is_home'),
            'status.required' => __('please_choose_category_status'),
            'status.enum_value' => __('category_status_invalid'),
            'slug.required' => __('please_enter_category_slug'),
        ];
    }
}

