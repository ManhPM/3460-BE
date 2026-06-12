<?php

namespace App\Admin\Http\Requests\VoucherProgram;

use App\Admin\Http\Requests\BaseRequest;
use App\Enums\Notification\NotificationType;
use Illuminate\Validation\Rules\Enum;

class GiveVoucherRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\VoucherProgram,id'],
            'user_id' => ['nullable'],
            'option' => ['required', new Enum(NotificationType::class)],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Mã chương trình voucher không được để trống.',
            'id.exists' => 'Chương trình voucher không hợp lệ.',
            'user_id.exists' => 'Người dùng không tồn tại.',
            'option.required' => 'Tùy chọn thông báo là bắt buộc.',
        ];
    }
}
