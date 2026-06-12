<?php

namespace App\Http\Requests\ShoppingCart;

use App\Admin\Http\Requests\BaseRequest;

class ChangeQtyRequest extends BaseRequest
{
    protected function methodPost()
    {
        return [
            'id' => ['required'],
            'code' => ['nullable', 'exists:App\Models\Discount,code'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required'],
            'qty' => ['required', 'integer', 'min:1'],
            'code' => ['nullable', 'exists:App\Models\Discount,code'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'ID giỏ hàng là bắt buộc.',
            'qty.required' => 'Số lượng là bắt buộc.',
            'qty.integer' => 'Số lượng phải là số nguyên.',
            'qty.min' => 'Số lượng phải lớn hơn hoặc bằng 1.',
            'code.exists' => 'Mã giảm giá không tồn tại.',
        ];
    }
}
