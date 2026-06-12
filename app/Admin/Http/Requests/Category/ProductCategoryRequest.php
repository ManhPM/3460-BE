<?php

namespace App\Admin\Http\Requests\Category;

use App\Admin\Http\Requests\BaseRequest;
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
                    $validator->errors()->add('parent_id', 'Chỉ cho phép tối đa 3 bậc trong cây danh mục.');
                }
            }
        });
    }
    public function messages()
    {
        return [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 30 ký tự.',
            'parent_id.exists' => 'Danh mục cha không hợp lệ.',
            'position.required' => 'Vị trí là bắt buộc.',
            'position.integer' => 'Vị trí phải là số nguyên.',
            'position.min' => 'Vị trí không thể nhỏ hơn 0.',
            'is_active.required' => 'Trạng thái hoạt động là bắt buộc.',
            'is_home.required' => 'Trạng thái hiển thị trên menu là bắt buộc.',
            'avatar.required' => 'Ảnh đại diện là bắt buộc.',
        ];
    }
}
