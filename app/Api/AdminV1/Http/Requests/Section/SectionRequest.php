<?php

namespace App\Api\AdminV1\Http\Requests\Section;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use Illuminate\Validation\Rules\Enum;

class SectionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\Category,id'],
            'title' => ['required', 'string'],
            'is_rightside' => ['required'],
            'position' => ['required'],
            'is_active' => ['required'],
            'avatar' => ['required'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Section,id'],
            'categories_id' => ['nullable', 'array'],
            'categories_id.*' => ['nullable', 'exists:App\Models\Category,id'],
            'title' => ['required', 'string'],
            'is_rightside' => ['required'],
            'is_active' => ['required'],
            'avatar' => ['required'],
            'position' => ['required'],
        ];
    }

    public function messages()
    {
        return [
            'categories_id.array' => __('category.array'),
            'categories_id.*.exists' => __('category.not_exists'),

            'title.required' => __('please_enter_title'),
            'title.string' => __('title_string'),

            'is_rightside.required' => __('section.is_rightside_required'),

            'position.required' => __('please_enter_position'),

            'is_active.required' => __('section.is_active_required'),

            'avatar.required' => __('please_enter_avatar'),

            'id.required' => __('please_enter_id'),
            'id.exists' => __('section.id_not_exists'),
        ];
    }
}
