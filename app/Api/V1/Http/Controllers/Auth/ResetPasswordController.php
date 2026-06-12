<?php

namespace App\Api\V1\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Api\V1\Http\Requests\Auth\PasswordResetUpdateRequest;
use App\Api\V1\Http\Requests\Auth\ResetPasswordRequest;
use App\Api\V1\Http\Requests\Auth\VerifyRequest;
use Illuminate\Support\Facades\Mail;
use App\Api\V1\Mail\Auth\ResetPassword;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\Auth\AuthServiceInterface;
use App\Mail\Authentication;
use Carbon\Carbon;

/**
 * @group Người dùng
 */
class ResetPasswordController extends Controller
{
    //
    public function __construct(
        UserRepositoryInterface $repository,
        AuthServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }
    /**
     * Lấy lại mật khẩu
     *
     * Lấy lại mật khẩu khi người dùng quên mật khẩu.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam email string required
     * Email Của bạn. Example: example@gmail.com
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công. Mã xác nhận đã được gửi về email của bạn."
     * }
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = $this->repository->findByField('email', $request->input('email'));
        $user->verify_code = random_int(1000, 9999);
        $user->verify_code_expiration = Carbon::now()->addMinutes(30);
        $user->save();
        Mail::to($user['email'])->send(new Authentication($user));

        return response()->json([
            'status' => 200,
            'message' => __('auth.verification_code_sent')
        ]);
    }

    /**
     * Cập nhật mật khẩu (Quên mật khẩu)
     *
     * Dùng để đổi mật khẩu mới khi đã xác minh thành công.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam email string required
     * Email Của bạn. Example: example@gmail.com
     *
     * @bodyParam password string required
     * Mật khẩu mới. Example: 1234
     *
     * @bodyParam password_confirmation string required
     * Lặp lại mật khẩu mới. Example: 1234
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     *
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện thất bại."
     * }
     */
    public function updatePassword(PasswordResetUpdateRequest $request)
    {
        try {
            $user = $this->repository->findByField('email', $request->input('email'));
            $password = bcrypt($request->input('password'));
            $user->update([
                'password' => $password,
                'verify_code' => null,
                'verify_code_expiration' => null,
            ]);
            return response()->json([
                'status' => 200,
                'message' => __('success')
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => 400,
                'message' => __('fail')
            ], 400);
        }
    }
}
