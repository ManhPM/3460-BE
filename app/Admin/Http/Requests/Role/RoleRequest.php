<?php

namespace App\Admin\Http\Requests\Role;

use App\Admin\Http\Requests\BaseRequest;

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
            'title.required' => 'Tiêu đề vai trò là bắt buộc.',
            'title.string' => 'Tiêu đề vai trò phải là một chuỗi.',

            'name.required' => 'Tên vai trò là bắt buộc.',
            'name.string' => 'Tên vai trò phải là một chuỗi.',

            'guard_name.required' => 'Guard name là bắt buộc.',
            'guard_name.string' => 'Guard name phải là một chuỗi.',

            'id.required' => 'ID vai trò là bắt buộc.',
            'id.exists' => 'Vai trò không hợp lệ.',
        ];
    }
}
