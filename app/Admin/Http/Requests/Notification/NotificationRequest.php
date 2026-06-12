<?php

namespace App\Admin\Http\Requests\Notification;

use App\Admin\Http\Requests\BaseRequest;

class NotificationRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'short_message' => ['required', 'string', 'max:191'],
            'message' => ['required', 'string', 'max:1000'],
            'user_id' => ['nullable'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Notification,id'],
            'title' => ['required', 'string', 'max:255'],
            'short_message' => ['required', 'string', 'max:191'],
            'message' => ['required', 'string', 'max:1000'],
            'avatar' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Vui lòng cung cấp ID của thông báo.',
            'id.exists' => 'Thông báo không tồn tại.',
            'title.required' => 'Vui lòng nhập tiêu đề thông báo.',
            'title.string' => 'Tiêu đề thông báo phải là một chuỗi ký tự.',
            'message.required' => 'Vui lòng nhập nội dung thông báo.',
            'type.required' => 'Vui lòng chọn loại thông báo.',
            'type' => 'Loại thông báo không hợp lệ.',
            'status.required' => 'Vui lòng chọn trạng thái của thông báo.',
            'status' => 'Trạng thái thông báo không hợp lệ.',
        ];
    }
}
