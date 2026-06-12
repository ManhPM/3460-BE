<?php

namespace App\Api\AdminV1\Http\Requests\Admin;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class AdminRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'email' => ['required', 'email', 'unique:App\Models\Admin,email'],
            'fullname' => ['required', 'string'],
            'phone' => ['required', 'unique:App\Models\Admin,phone'],
            'password' => ['required', 'string', 'confirmed'],
            'branch_name' => ['nullable', 'string'],
            'branch_phone' => ['nullable', 'string'],
            'branch_address' => ['nullable', 'string'],
            'avatar' => ['nullable'],
            'role_id' => ['nullable', 'integer'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Admin,id'],
            'email' => ['required', 'email', 'unique:App\Models\Admin,email,' . $this->id],
            'fullname' => ['required', 'string'],
            'phone' => ['required', 'unique:App\Models\Admin,phone,' . $this->id],
            'password' => ['nullable', 'max:191'],
            'branch_name' => ['nullable', 'string'],
            'branch_phone' => ['nullable', 'string'],
            'branch_address' => ['nullable', 'string'],
            'avatar' => ['nullable'],
            'role_id' => ['nullable', 'integer'],
        ];
    }

    public function messages()
    {
        return [
            'email.required' => __('please_enter_email'),
            'email.email' => __('admin_email_invalid'),
            'email.unique' => __('admin_email_unique'),
            'fullname.required' => __('please_enter_fullname'),
            'fullname.string' => __('admin_fullname_string'),
            'phone.required' => __('please_enter_phone'),

            'phone.unique' => __('admin_phone_unique'),
            'password.required' => __('please_enter_password'),
            'password.string' => __('admin_password_string'),
            'password.confirmed' => __('admin_password_confirmed'),
            'id.required' => __('please_enter_admin_id'),
            'id.exists' => __('admin_id_not_exists'),
        ];
    }
}
