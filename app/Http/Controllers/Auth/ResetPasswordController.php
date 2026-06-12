<?php

namespace App\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Traits\Setup;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\SendMailResetPasswordRequest;
use App\Http\Requests\Auth\VerifyResetPasswordRequest;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\Auth\AuthServiceInterface;
use App\Mail\ResetPasswordMail;
use Illuminate\Support\Facades\Mail;

class ResetPasswordController extends Controller
{
    use Setup;
    public function __construct(
        UserRepositoryInterface $repository,
        AuthServiceInterface $service
    )
    {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }
    public function getView()
    {
        return [
            'edit' => 'auth.password.reset.edit',
            'success' => 'auth.password.reset.success',
        ];
    }

    public function getRoute()
    {
        return [
            'success' => 'password.reset.success',
        ];
    }
    public function success(){
        return view($this->view['success']);
    }
    public function edit(SendMailResetPasswordRequest $request) {
        $data = $request->validated();

        $user = $this->repository->findByField('email', $data['email']);
        $token = $this->createTokenForgotPassword();

        if ($user) {
            $this->repository->update($user->id, ['token_get_password' => $token]);
            Mail::to($data['email'])->send(new ResetPasswordMail($token));
            return response()->json([
                'success' => true,
                'message' => 'Token reset password đã được tạo và cập nhật.',
                'token' => $token
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy người dùng với email này.'
            ], 404);
        }
    }

    public function verify(VerifyResetPasswordRequest $request) {
        $data = $request->validated();

        $user = $this->repository->findByField('email', $data['email']);

        if ($user->token_get_password == $data['token_get_password']) {
            return response()->json([
                'success' => true,
                'message' => 'Mã xác nhận chính xác.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Mã xác nhận không chính xác.'
            ], 400);
        }
    }

    public function update(ResetPasswordRequest $request){
        $data = $request->validated();
        $data['password'] = bcrypt($request->input('new_password_reset'));
        $user = $this->repository->findByField('email', $data['email']);
        $this->repository->update($user->id, ['password' => $data['password']]);
        return back()->with('success', __('Lấy lại mật khẩu thành công'));
    }
}
