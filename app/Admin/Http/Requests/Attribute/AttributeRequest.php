<?php

namespace App\Admin\Http\Requests\Attribute;

use App\Admin\Http\Requests\BaseRequest;
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
            'type.required' => 'Loại thuộc tính là bắt buộc.',
            'name.required' => 'Tên thuộc tính là bắt buộc.',
            'name.string' => 'Tên thuộc tính phải là chuỗi ký tự.',
            'position.required' => 'Vị trí là bắt buộc.',
            'position.integer' => 'Vị trí phải là số nguyên.',
            'position.min' => 'Vị trí không được nhỏ hơn 0.',
            'id.required' => 'ID thuộc tính là bắt buộc.',
            'id.exists' => 'Thuộc tính không tồn tại.',
        ];
    }
}
