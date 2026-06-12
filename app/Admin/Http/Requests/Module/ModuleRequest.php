<?php

namespace App\Admin\Http\Requests\Module;

use App\Admin\Http\Requests\BaseRequest;
use BenSampo\Enum\Rules\EnumValue;
use App\Enums\Module\ModuleStatus;

class ModuleRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    protected function methodPost()
    {
        return [
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new EnumValue(ModuleStatus::class, false)],
        ];
    }

    protected function methodPut()
    {
        return [
            'id' => ['required', 'exists:App\Models\Module,id'],
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'status' => ['required', new EnumValue(ModuleStatus::class, false)],
        ];
    }

    public function messages()
    {
        return [
            'id.required' => 'Vui lòng cung cấp ID của module.',
            'id.exists' => 'Module không tồn tại.',
            'name.required' => 'Vui lòng nhập tên module.',
            'name.string' => 'Tên module phải là một chuỗi ký tự.',
            'description.string' => 'Mô tả phải là một chuỗi ký tự.',
            'status.required' => 'Vui lòng chọn trạng thái của module.',
        ];
    }
}
