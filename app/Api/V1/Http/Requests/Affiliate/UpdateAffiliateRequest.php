<?php

namespace App\Api\V1\Http\Requests\Affiliate;

use App\Api\V1\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class UpdateAffiliateRequest extends BaseRequest
{
    protected function methodPost()
    {
        $userId = auth()->id();
        return [
            'affiliate_code' => [
                'nullable',
                'string',
                'min:3',
                'max:50',
                'regex:/^[a-zA-Z0-9_\-]+$/',
                Rule::unique('users', 'affiliate_code')->ignore($userId),
            ],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:255'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages()
    {
        return [
            'affiliate_code.min' => 'Mã giới thiệu phải có tối thiểu 3 ký tự.',
            'affiliate_code.max' => 'Mã giới thiệu không được vượt quá 50 ký tự.',
            'affiliate_code.regex' => 'Mã giới thiệu chỉ được chứa chữ cái, số, dấu gạch dưới hoặc gạch ngang (không dấu, không khoảng trắng).',
            'affiliate_code.unique' => 'Mã giới thiệu này đã được sử dụng. Vui lòng chọn mã khác.',
        ];
    }
}

