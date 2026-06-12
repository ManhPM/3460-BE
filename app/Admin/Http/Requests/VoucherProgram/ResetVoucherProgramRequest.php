<?php

namespace App\Admin\Http\Requests\VoucherProgram;

use App\Admin\Http\Requests\BaseRequest;

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
            'id.required' => 'Mã chương trình voucher không được để trống.',
            'id.exists' => 'Chương trình voucher không hợp lệ hoặc không tồn tại.',
        ];
    }
}
