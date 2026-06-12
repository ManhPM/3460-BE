<?php

namespace App\Admin\Http\Requests\Slider;

use App\Admin\Http\Requests\BaseRequest;

class SliderItemRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'title' => ['required', 'string'],
            'slider_id' => ['required', 'exists:App\Models\Slider,id'],
            'link' => ['nullable'],
            'position' => ['required', 'integer'],
            'avatar' => ['required'],
            'mobile_avatar' => ['required']
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\SliderItem,id'],
            'slider_id' => ['required', 'exists:App\Models\Slider,id'],
            'title' => ['required', 'string'],
            'link' => ['nullable'],
            'position' => ['required', 'integer'],
            'avatar' => ['required'],
            'mobile_avatar' => ['required']
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Tiêu đề là bắt buộc.',
            'title.string' => 'Tiêu đề phải là chuỗi.',

            'slider_id.required' => 'Slider ID là bắt buộc.',
            'slider_id.exists' => 'Slider ID không hợp lệ.',

            'link.string' => 'Liên kết phải là chuỗi.',

            'position.required' => 'Vị trí là bắt buộc.',
            'position.integer' => 'Vị trí phải là số nguyên.',

            'avatar.required' => 'Ảnh là bắt buộc.',
            'mobile_avatar.required' => 'Ảnh mobile là bắt buộc.',

            'id.required' => 'ID là bắt buộc.',
            'id.exists' => 'ID không tồn tại trong hệ thống.',
        ];
    }
}
