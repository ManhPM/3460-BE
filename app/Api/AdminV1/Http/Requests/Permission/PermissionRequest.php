<?php

namespace App\Api\AdminV1\Http\Requests\Permission;

use App\Api\AdminV1\Http\Requests\BaseRequest;
use App\Enums\Permission\PermissionType;
use Illuminate\Validation\Rules\Enum;

class PermissionRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'title' => ['required', 'string'],
            'name' => ['required', 'string'],
            'guard_name' => ['required', 'string'],
            'module_id' => ['nullable', 'int'],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Permission,id'],
            'title' => ['required', 'string'],
            'name' => ['required', 'string'],
            'guard_name' => ['required', 'string'],
            'module_id' => ['nullable', 'int'],
        ];
    }

    public function messages()
    {
        return [
            'title.required' => __('please_enter_title'),
            'name.required' => __('please_enter_permission_name'),
            'name.unique' => __('permission.name_unique'),
            'guard_name.required' => __('please_enter_guard_name'),
            'module_id.integer' => __('permission.module_id_integer'),
            'id.required' => __('please_enter_id_when_update'),
            'id.exists' => __('permission.not_exists'),
        ];
    }
}
