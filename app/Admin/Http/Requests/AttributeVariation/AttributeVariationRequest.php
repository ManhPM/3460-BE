<?php

namespace App\Admin\Http\Requests\AttributeVariation;

use App\Admin\Http\Requests\BaseRequest;
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
            'attribute_id.required' => 'Thuộc tính là bắt buộc.',
            'attribute_id.exists' => 'Thuộc tính không tồn tại.',
            'name.required' => 'Tên biến thể là bắt buộc.',
            'name.string' => 'Tên biến thể phải là chuỗi ký tự.',
            'position.required' => 'Vị trí là bắt buộc.',
            'position.integer' => 'Vị trí phải là số nguyên.',
            'position.min' => 'Vị trí không được nhỏ hơn 0.',
            'meta_value.array' => 'Meta value phải là một mảng hợp lệ.',
            'meta_value.color.string' => 'Màu sắc phải là một chuỗi hợp lệ.',
            'meta_value.color.regex' => 'Màu sắc phải có định dạng hợp lệ (#RRGGBB hoặc #RGB).',
        ];
    }
}
