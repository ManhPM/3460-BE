<?php

namespace App\Api\AdminV1\Http\Requests\Role;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class RoleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost(): array
    {
        return [
            'title' => ['required', 'string'],
            'name' => ['required', 'string'],
            'guard_name' => ['required', 'string'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\Role,id'],
            'title' => ['required', 'string'],
            'guard_name' => ['required', 'string'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('please_enter_role_title'),
            'title.string' => __('role_title_string'),

            'name.required' => __('please_enter_role_name'),
            'name.string' => __('role_name_string'),

            'guard_name.required' => __('please_enter_guard_name'),
            'guard_name.string' => __('role_guard_name_string'),

            'id.required' => __('please_enter_role_id'),
            'id.exists' => __('role_id_invalid'),
        ];
    }
}

