<?php

namespace App\Api\AdminV1\Http\Requests\Auth;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class OauthReqest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodGet()
    {
        return [
            'code' => 'required|exists:users,code',
        ];
    }
    protected function methodPost()
    {
        return [
            'code' => 'required',
            'verify_code' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'code.required' => __('please_enter_verification_code'),
            'code.exists' => __('oauth.code_invalid'),
            'verify_code.required' => __('please_enter_activation_token'),
            'verify_code.numeric' => __('oauth.verify_code_numeric'),
        ];
    }
}

