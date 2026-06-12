<?php

namespace App\Api\AdminV1\Http\Requests\Slider;

use App\Api\AdminV1\Http\Requests\BaseRequest;

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
            'title.required' => __('please_enter_title'),
            'title.string' => __('title_string'),

            'slider_id.required' => __('please_enter_slider_id'),
            'slider_id.exists' => __('slider.not_exists'),

            'link.string' => __('slider_item.link_string'),

            'position.required' => __('please_enter_position'),
            'position.integer' => __('position_numeric'),

            'avatar.required' => __('please_enter_avatar'),
            'mobile_avatar.required' => __('please_enter_mobile_avatar'),

            'id.required' => __('please_enter_id'),
            'id.exists' => __('slider_item.id_not_exists'),
        ];
    }
}
