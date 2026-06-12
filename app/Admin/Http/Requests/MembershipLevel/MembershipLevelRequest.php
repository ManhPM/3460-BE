<?php

namespace App\Admin\Http\Requests\MembershipLevel;

use App\Admin\Http\Requests\BaseRequest;

class MembershipLevelRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'unique:App\Models\MembershipLevel,name'],
            'min_points' => ['required', 'numeric', 'unique:App\Models\MembershipLevel,min_points'],
            'color_1' => ['required'],
            'color_2' => ['required'],
            'color_3' => ['required'],
            'icon' => ['required'],
            'description' => ['nullable'],
            'discount_percentage' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\MembershipLevel,id'],
            'name' => ['required', 'unique:App\Models\MembershipLevel,name,' . $this->id],
            'min_points' => ['required', 'numeric', 'unique:App\Models\MembershipLevel,min_points,' . $this->id],
            'color_1' => ['required'],
            'color_2' => ['required'],
            'color_3' => ['required'],
            'icon' => ['required'],
            'description' => ['nullable'],
            'discount_percentage' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Vui lòng nhập tên hạng thành viên.',
            'name.unique' => 'Tên hạng thành viên đã tồn tại.',
            'min_points.required' => 'Vui lòng nhập số điểm tối thiểu.',
            'min_points.numeric' => 'Số điểm tối thiểu phải là một số.',
            'min_points.unique' => 'Số điểm tối thiểu đã được sử dụng cho một hạng thành viên khác.',
            'color_1.required' => 'Vui lòng chọn màu chính.',
            'color_2.required' => 'Vui lòng chọn màu phụ.',
            'color_3.required' => 'Vui lòng chọn màu thứ ba.',
            'icon.required' => 'Vui lòng tải lên biểu tượng cho hạng thành viên.',
            'id.required' => 'ID hạng thành viên là bắt buộc khi cập nhật.',
            'id.exists' => 'Hạng thành viên không tồn tại.',
        ];
    }
}
