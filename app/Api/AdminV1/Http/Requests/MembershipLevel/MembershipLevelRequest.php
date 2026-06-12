<?php

namespace App\Api\AdminV1\Http\Requests\MembershipLevel;

use App\Api\AdminV1\Http\Requests\BaseRequest;

class MembershipLevelRequest extends BaseRequest
{
    protected function methodPost(): array
    {
        return [
            'name' => ['required', 'unique:App\Models\MembershipLevel,name'],
            'min_points' => ['required', 'numeric', 'unique:App\Models\MembershipLevel,min_points'],
            'color_1' => ['required'],
            'color_2' => ['required'],
            'color_3' => ['required'],
            'icon' => ['required'],
            'description' => ['nullable'],
            'discount_percentage' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    protected function methodPut(): array
    {
        return [
            'id' => ['required', 'exists:App\Models\MembershipLevel,id'],
            'name' => ['required', 'unique:App\Models\MembershipLevel,name,' . $this->id],
            'min_points' => ['required', 'numeric', 'unique:App\Models\MembershipLevel,min_points,' . $this->id],
            'color_1' => ['required'],
            'color_2' => ['required'],
            'color_3' => ['required'],
            'icon' => ['required'],
            'description' => ['nullable'],
            'discount_percentage' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }

    public function messages()
    {
        return [
            'name.required' => __('please_enter_membership_level_name'),
            'name.unique' => __('membership_level.name_unique'),
            'min_points.required' => __('please_enter_min_points'),
            'min_points.numeric' => __('membership_level.min_points_numeric'),
            'min_points.unique' => __('membership_level.min_points_unique'),
            'color_1.required' => __('please_choose_color_1'),
            'color_2.required' => __('please_choose_color_2'),
            'color_3.required' => __('please_choose_color_3'),
            'icon.required' => __('please_upload_icon'),
            'id.required' => __('please_enter_membership_level_id'),
            'id.exists' => __('membership_level.not_exists'),
        ];
    }
}

