<?php

namespace App\Api\V1\Http\Requests\Auth;

use App\Api\V1\Http\Requests\BaseRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Validator;

class LoginRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required',
            'password' => 'required'
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator(Validator $validator)
    {
        $validator->after(function ($validator) {
            $username = $this->username;
            $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);

            if ($isEmail) {
                // Kiểm tra email tồn tại
                $exists = DB::table('users')->where('email', $username)->exists();
                if (!$exists) {
                    $validator->errors()->add('username', __('account_not_exists'));
                }
            } else {


                // Kiểm tra số điện thoại có tồn tại và đã xác thực chưa
                $user = User::where('phone', $username)->where('is_phone_verified', 1)->first();
                if (!$user) {
                    $validator->errors()->add('username', __('phone_not_verified'));
                }
            }
        });
    }
}
