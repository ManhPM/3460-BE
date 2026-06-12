<?php

namespace App\Api\AdminV1\Http\Requests\VoucherProgram;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
            'id.required' => __('please_enter_voucher_program_id'),
            'id.exists' => __('voucher_program.not_exists'),
            'user_id.exists' => __('user_not_found'),
            'option.required' => __('please_choose_notification_option'),
        ];
    }
}
