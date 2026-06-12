<?php

namespace App\Admin\Http\Requests\Permission;

use App\Admin\Http\Requests\BaseRequest;
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
            'title.required' => 'Tiêu đề là bắt buộc.',
            'name.required' => 'Tên quyền là bắt buộc.',
            'name.unique' => 'Tên quyền đã tồn tại.',
            'guard_name.required' => 'Guard name là bắt buộc.',
            'module_id.integer' => 'Module ID phải là số nguyên.',
            'id.required' => 'ID là bắt buộc khi cập nhật.',
            'id.exists' => 'ID không tồn tại trong hệ thống.',
        ];
    }
}
