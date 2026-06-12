<?php

namespace App\Admin\Http\Requests\Section;

use App\Admin\Http\Requests\BaseRequest;
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
            'categories_id.array' => 'Danh mục phải là một mảng hợp lệ.',
            'categories_id.*.exists' => 'Một hoặc nhiều danh mục không hợp lệ.',

            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là một chuỗi.',

            'is_rightside.required' => 'Trường "is_rightside" là bắt buộc.',

            'position.required' => 'Vị trí là bắt buộc.',

            'is_active.required' => 'Trạng thái kích hoạt là bắt buộc.',

            'avatar.required' => 'Ảnh đại diện là bắt buộc.',

            'id.required' => 'ID là bắt buộc.',
            'id.exists' => 'ID không tồn tại trong hệ thống.',
        ];
    }
}
