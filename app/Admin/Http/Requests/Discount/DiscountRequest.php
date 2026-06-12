<?php

namespace App\Admin\Http\Requests\Discount;

use App\Admin\Http\Requests\BaseRequest;
use App\Models\Discount;
use Illuminate\Support\Facades\DB;

class DiscountRequest extends BaseRequest
{
    protected function prepareForValidation()
    {
        $this->merge([
            'discount_value' => $this->cleanDiscountValue($this->input('discount_value')),
        ]);
    }

    private function cleanDiscountValue($value)
    {
        return is_string($value) ? preg_replace('/( VND| %|,)/', '', $value) : $value;
    }

    protected function methodPost(): array
    {
        return [
            'code' => ['required'],
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'max_usage' => ['required', 'numeric', 'min: 1'],
            'min_order_amount' => ['required', 'numeric', 'min: 1'],
            'discount_value' => ['required'],
            'max_discount_value' => ['required', 'numeric', 'min: 1'],
            'max_usage_per_user' => ['required', 'numeric', 'min: 1'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Discount,id'],
            'code' => ['required'],
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'max_usage' => ['required', 'numeric', 'min: 1'],
            'min_order_amount' => ['required', 'numeric', 'min: 1'],
            'discount_value' => ['required'],
            'max_discount_value' => ['required', 'numeric', 'min: 1'],
            'max_usage_per_user' => ['required', 'numeric', 'min: 1'],
        ];
    }

    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $code = $this->code;

            $discountExist = Discount::where('code', $code)->first();
            if ($this->isMethod('put')) {
                if ($discountExist && $discountExist->id != $this->id) {
                    $validator->errors()->add('code', 'Mã giảm giá đã tồn tại.');
                }
            }
            if ($this->isMethod('post')) {
                if ($discountExist) {
                    $validator->errors()->add('code', 'Mã giảm giá đã tồn tại.');
                }
            }

            $voucherExists = DB::table('vouchers')
                ->where('code', $code)
                ->exists();

            if ($voucherExists) {
                $validator->errors()->add('code', 'Mã giảm giá đã tồn tại.');
            }
        });
    }

    public function messages()
    {
        return [
            'code.required' => 'Vui lòng nhập mã giảm giá.',
            'date_start.required' => 'Ngày bắt đầu là bắt buộc.',
            'date_end.required' => 'Ngày kết thúc là bắt buộc.',
            'date_end.after_or_equal' => 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu.',
            'max_usage.required' => 'Vui lòng nhập số lần sử dụng tối đa.',
            'max_usage.numeric' => 'Số lần sử dụng phải là số.',
            'max_usage.min' => 'Số lần sử dụng phải lớn hơn hoặc bằng 1.',
            'min_order_amount.required' => 'Vui lòng nhập số tiền tối thiểu để áp dụng giảm giá.',
            'min_order_amount.numeric' => 'Số tiền tối thiểu phải là số.',
            'min_order_amount.min' => 'Số tiền tối thiểu phải lớn hơn hoặc bằng 1.',
            'discount_value.required' => 'Vui lòng nhập giá trị giảm giá.',
            'max_discount_value.required' => 'Vui lòng nhập giá trị giảm giá tối đa.',
            'max_discount_value.numeric' => 'Giá trị giảm giá tối đa phải là số.',
            'max_discount_value.min' => 'Giá trị giảm giá tối đa phải lớn hơn hoặc bằng 1.',
            'max_usage_per_user.required' => 'Vui lòng nhập số lần sử dụng tối đa cho mỗi người dùng.',
            'max_usage_per_user.numeric' => 'Số lần sử dụng tối đa phải là số.',
            'max_usage_per_user.min' => 'Số lần sử dụng tối đa phải lớn hơn hoặc bằng 1.',
            'id.required' => 'ID giảm giá là bắt buộc khi cập nhật.',
            'id.exists' => 'Mã giảm giá không tồn tại trong hệ thống.',
        ];
    }
}
