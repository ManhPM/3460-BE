<?php

namespace App\Admin\Http\Requests\ShippingRate;

use App\Admin\Http\Requests\BaseRequest;

class ShippingRateRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'price' => ['required', 'numeric', 'min:1'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\ShippingRate,id'],
            'province_id' => ['required', 'exists:App\Models\Province,id'],
            'ward_id' => ['nullable', 'exists:App\Models\Ward,id'],
            'price' => ['required', 'numeric', 'min:1'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {});
    }

    public function messages()
    {
        return [
            'province_id.required' => 'Tỉnh/Thành phố là bắt buộc.',
            'province_id.exists' => 'Tỉnh/Thành phố không hợp lệ.',

            'ward_id.exists' => 'Thành phố/Khu vực không hợp lệ.',

            'price.required' => 'Giá vận chuyển là bắt buộc.',
            'price.numeric' => 'Giá vận chuyển phải là một số.',
            'price.min' => 'Giá vận chuyển phải lớn hơn hoặc bằng 1.',

            'id.required' => 'ID là bắt buộc.',
            'id.exists' => 'ID không tồn tại trong hệ thống.',
        ];
    }
}
