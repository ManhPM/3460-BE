<?php

namespace App\Api\AdminV1\Http\Requests\Module;

use App\Api\AdminV1\Http\Requests\BaseRequest;
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
            'id.required' => __('please_provide_module_id'),
            'id.exists' => __('module.not_exists'),
            'name.required' => __('please_enter_module_name'),
            'name.string' => __('module.name_string'),
            'description.string' => __('module.description_string'),
            'status.required' => __('please_choose_module_status'),
        ];
    }
}
