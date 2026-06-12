<?php

namespace App\Api\AdminV1\Http\Requests\Slider;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\Slider\SliderStatus;

class SliderRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'status' => ['required', new EnumValue(SliderStatus::class, false)],
            'name' => ['required', 'string'],
            'plain_key' => ['required', 'string', 'unique:App\Models\Slider,plain_key'],
            'desc' => ['nullable'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Slider,id'],
            'status' => ['required', new EnumValue(SliderStatus::class, false)],
            'name' => ['required', 'string'],
            'plain_key' => ['required', 'string', 'unique:App\Models\Slider,plain_key,' . $this->id],
            'desc' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'status.required' => __('please_choose_status'),
            'status.enum' => __('slider.status_invalid'),

            'name.required' => __('please_enter_name'),
            'name.string' => __('name_string'),

            'plain_key.required' => __('please_enter_plain_key'),
            'plain_key.string' => __('slider.plain_key_string'),
            'plain_key.unique' => __('slider.plain_key_unique'),

            'id.required' => __('please_enter_id'),
            'id.exists' => __('slider.not_exists'),

            'desc.string' => __('desc_string'),
        ];
    }
}
