<?php

namespace App\Api\AdminV1\Http\Requests\Attribute;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Attribute\AttributeType;
use Illuminate\Validation\Rules\Enum;

class AttributeRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'type' => ['required', new Enum(AttributeType::class)],
            'name' => ['required', 'string'],
            'position' => ['required', 'integer', 'min:0'],
            'desc' => ['nullable'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Attribute,id'],
            'type' => ['required', new Enum(AttributeType::class)],
            'name' => ['required', 'string'],
            'position' => ['required', 'integer', 'min:0'],
            'desc' => ['nullable'],
        ];
    }

    public function messages()
    {
        return [
            'type.required' => __('please_choose_attribute_type'),
            'name.required' => __('please_enter_attribute_name'),
            'name.string' => __('name_string'),
            'position.required' => __('please_enter_position'),
            'position.integer' => __('position_numeric'),
            'position.min' => __('position_min'),
            'id.required' => __('please_enter_attribute_id'),
            'id.exists' => __('attribute.not_exists'),
        ];
    }
}

