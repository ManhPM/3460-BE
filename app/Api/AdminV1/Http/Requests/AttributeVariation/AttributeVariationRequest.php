<?php

namespace App\Api\AdminV1\Http\Requests\AttributeVariation;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use BenSampo\Enum\Rules\EnumValue;

class AttributeVariationRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'attribute_id' => ['required', 'exists:App\Models\Attribute,id'],
            'name' => ['required', 'string'],
            'position' => ['required', 'integer'],
            'meta_value' => ['nullable', 'array'],
            'meta_value[color]' => ['nullable'],
            'desc' => ['nullable'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\AttributeVariation,id'],
            'name' => ['required', 'string'],
            'position' => ['required', 'integer'],
            'meta_value' => ['nullable', 'array'],
            'meta_value[color]' => ['nullable'],
            'desc' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'attribute_id.required' => __('please_choose_attribute'),
            'attribute_id.exists' => __('attribute.not_exists'),
            'name.required' => __('please_enter_attribute_variation_name'),
            'name.string' => __('attribute_variation.name_string'),
            'position.required' => __('please_enter_position'),
            'position.integer' => __('position_numeric'),
            'position.min' => __('position_min'),
            'meta_value.array' => __('attribute_variation.meta_value_array'),
            'meta_value.color.string' => __('attribute_variation.color_string'),
            'meta_value.color.regex' => __('attribute_variation.color_regex'),
        ];
    }
}
