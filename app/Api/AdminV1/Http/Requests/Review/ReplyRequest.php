<?php

namespace App\Api\AdminV1\Http\Requests\Review;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class ReplyRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'admin_reply' => ['required', 'string'],
        ];
    }
}
