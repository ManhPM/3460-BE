<?php

namespace App\Api\AdminV1\Http\Requests\Notification;

use App\Api\AdminV1\Http\Requests\BaseRequest;

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
            'id.required' => __('please_provide_notification_id'),
            'id.exists' => __('notification.not_exists'),
            'title.required' => __('please_enter_notification_title'),
            'title.string' => __('notification.title_string'),
            'message.required' => __('please_enter_notification_message'),
            'type.required' => __('please_choose_notification_type'),
            'type' => __('notification.type_invalid'),
            'status.required' => __('please_choose_notification_status'),
            'status' => __('notification.status_invalid'),
        ];
    }
}
