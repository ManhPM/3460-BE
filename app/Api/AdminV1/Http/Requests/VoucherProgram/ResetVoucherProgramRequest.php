<?php

namespace App\Api\AdminV1\Http\Requests\VoucherProgram;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class ResetVoucherProgramRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'id' => ['required', 'exists:voucher_programs,id'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => __('please_enter_voucher_program_id'),
            'id.exists' => __('voucher_program.not_exists'),
        ];
    }
}
