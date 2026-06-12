<?php

namespace App\Api\AdminV1\Http\Requests\Category;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Admin\Rules\Category\CategoryParent;
use App\Enums\Category\HomeSliderOption;
use App\Models\Category;
use Illuminate\Validation\Rules\Enum;

class ProductCategoryRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'string', 'max:30'],
            'parent_id' => ['nullable', 'exists:App\Models\Category,id'],
            'position' => ['required', 'integer', 'min:0'],
            'is_active' => ['required'],
            'is_home' => ['nullable'],
            'icon' => ['nullable'],
            'avatar' => ['required']
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Category,id'],
            'name' => ['required', 'string', 'max:30'],
            'parent_id' => ['nullable', 'exists:App\Models\Category,id', new CategoryParent($this->id)],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['required'],
            'is_home' => ['nullable'],
            'icon' => ['nullable'],
            'avatar' => ['required']
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $parentId = $this->input('parent_id');

            if ($parentId) {
                // Lấy danh sách các cha để kiểm tra cấp độ
                $parentHierarchy = [];
                $currentParentId = $parentId;

                while ($currentParentId) {
                    $category = Category::find($currentParentId);
                    if ($category) {
                        $parentHierarchy[] = $category->id;
                        $currentParentId = $category->parent_id;
                    } else {
                        break;
                    }
                }

                if (count($parentHierarchy) >= 3) {
                    $validator->errors()->add('parent_id', __('category.max_level_3'));
                }
            }
        });
    }
    public function messages()
    {
        return [
            'name.required' => __('please_enter_category_name'),
            'name.string' => __('category_name_string'),
            'name.max' => __('category.name_max'),
            'parent_id.exists' => __('category_parent_id_invalid'),
            'position.required' => __('please_enter_position'),
            'position.integer' => __('position_numeric'),
            'position.min' => __('position_min'),
            'is_active.required' => __('please_choose_active_status'),
            'is_home.required' => __('category.is_home_required'),
            'avatar.required' => __('please_enter_avatar'),
        ];
    }
}
