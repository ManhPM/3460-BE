<?php

namespace App\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Auth\ForgotPasswordRequest;
use App\Admin\Http\Requests\Auth\LoginUserRequest;
use App\Admin\Http\Requests\Auth\OauthReqest;
use App\Admin\Http\Requests\Auth\RegisterRequest;
use App\Admin\Http\Requests\Auth\ResendOTPRequest;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use App\Admin\Traits\Setup;
use App\Enums\User\Gender;
use App\Mail\Authentication;
use App\Mail\ForgotPass;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    use Setup, Roles;
    private $login;

    protected $repository;
    protected $service;

    protected $view;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;

        $this->view = $this->getView();
    }

    public function getView()
    {
        return [
            'indexUser' => 'user.auth.login',
            'forgot-password' => 'user.auth.forgot-password',
        ];
    }

    public function indexUser()
    {
        return view($this->view['indexUser']);
    }

    public function forgotPassword()
    {
        return view($this->view['forgot-password']);
    }

    public function forgotPasswordSend(ForgotPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $user = $this->repository->findByField('email', $data['email']);

            if ($user) {

                $user['token_get_password'] = Str::random(64);
                $user['verify_code_expiration'] = Carbon::now()->addMinutes(30);
                $user['url'] = route('user.auth.resetPassword', ['token_get_password' => $user['token_get_password']]);
                $this->repository->update($user['id'], ['token_get_password' => $user['token_get_password'], 'verify_code_expiration' => $user['verify_code_expiration']]);
                Mail::to($data['email'])->send(new ForgotPass($user));
                DB::commit();
                return back()->with('success', __('Yêu cầu đã được xác nhận, hãy kiểm tra email.'));
            }

            DB::commit();
            return back()->with('error', __('Thực hiện không thành công!'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }

    public function resendOTP(ResendOTPRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $user = $this->repository->findByField('code', $data['code']);

            if ($user) {
                $verifyCode = random_int(1000, 9999);
                $user->update([
                    'verify_code' => $verifyCode,
                    'verify_code_expiration' => Carbon::now()->addMinutes(30)
                ]);

                Mail::to($user['email'])->send(new Authentication([
                    'fullname' => $user->fullname,
                    'verify_code' => $user->verify_code,
                    'email' => $user->email,
                ]));

                DB::commit();
                return redirect()->intended(route('user.auth.oauth', ['code' => $data['code']]))->with('success', __('Mã xác minh đã được gửi tới email của bạn.'));
            }

            DB::commit();
            return back()->with('error', __('Email chưa được đăng ký. Vui lòng đăng ký!'));
        } catch (\Exception $e) {
            throw $e;
            DB::rollBack();
            return back()->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }

    public function resetPassword(Request $request)
    {
        DB::beginTransaction();
        try {
            $token = $request->query('token_get_password');
            if ($this->checkToken($token) === true) {
                DB::commit();
                return view('user.auth.change-forgot', compact('token'));
            }

            DB::commit();
            return redirect()->route('user.auth.indexUser')->with('error', __('Token đã hết hạn'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.auth.indexUser')->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }

    public function checkToken(string $token)
    {
        $user = $this->repository->findByField('token_get_password', $token);
        if ($user) {
            if ($user['verify_code_expiration'] > now()) {
                return true;
            }
        }
        return false;
    }

    public function changePassword(ForgotPasswordRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->all();
            $user = $this->repository->findByField('token_get_password', $data['token_get_password']);
            $user['password'] = Hash::make($data['password']);
            $user['token_get_password'] = null;
            $user['verify_code_expiration'] = null;
            $this->repository->update($user['id'], ['password' => $user['password'], 'token_get_password' => $user['token_get_password'], 'verify_code_expiration' => $user['verify_code_expiration']]);
            DB::commit();
            return redirect()->route('user.auth.indexUser')->with('success', __('Thay đổi mật khẩu thành công.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('user.auth.indexUser')->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }

    public function loginUser(LoginUserRequest $request)
    {
        try {
            $this->login = $request->validated();

            Auth::guard('admin')->logout();

            if (filter_var($this->login['username'], FILTER_VALIDATE_EMAIL)) {
                $result = $this->resolveWeb('email');
            } else {
                $result = $this->resolveWeb('phone');
            }

            if ($result) {
                // Tạo session mới sau khi xác thực
                $request->session()->regenerate();
                return $this->handleUserLogin();
            }

            return back()->with('error', __('Tên đăng nhập hoặc mật khẩu không đúng'));
        } catch (\Exception $e) {
            throw $e;
            return back()->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }


    protected function handleUserLogin()
    {
        if (Auth::guard('web')->check()) {
            $user = auth('web')->user();
            if ($user->is_email_verified == 0 && filter_var($this->login['username'], FILTER_VALIDATE_EMAIL)) {
                // Generate a new activation token
                $user->verify_code = random_int(1000, 9999);
                $this->repository->update($user->id, [
                    'verify_code' => $user->verify_code,
                    'verify_code_expiration' => Carbon::now()->addMinutes(30)
                ]);

                Auth::guard('web')->logout();

                Mail::to($user['email'])->send(new Authentication($user));

                // Redirect to the activation route
                return redirect()->intended(route('user.auth.oauth', ['code' => $user->code]))->with('error', __('Tài khoản của bạn chưa được kích hoạt. Vui lòng kiểm tra email để kích hoạt tài khoản.'));
            }
            return redirect()->intended(route('user.profile.indexUser'))->with('success', __('Đăng nhập thành công'));
        }
    }

    protected function resolveWeb($username)
    {
        if ($username == 'email') {
            return Auth::guard('web')->attempt(
                [
                    'email' => $this->login['username'],
                    'password' => $this->login['password'],
                ],
                isset($this->login['remember'])
            );
        } else {
            return Auth::guard('web')->attempt(
                [
                    'phone' => $this->login['username'],
                    'password' => $this->login['password'],
                ],
                isset($this->login['remember'])
            );
        }
    }

    public function register(RegisterRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['password'] = Hash::make($data['password']);
            $data['code'] = $this->createCodeUser();
            $data['verify_code'] = random_int(1000, 9999);
            $data['verify_code_expiration'] = Carbon::now()->addMinutes(30);
            if (env('IS_VERIFY_EMAIL')) {
                $data['is_email_verified'] = 0;
            } else {
                $data['is_email_verified'] = 1;
            }
            $data['gender'] = Gender::Male;

            $data['affiliate_code'] = $this->createAffiliateCode();

            // Tìm user theo email
            $user = $this->repository->findByField('email', $data['email']);
            Auth::guard('admin')->logout();
            if ($user) {
                // Cập nhật toàn bộ thông tin của user
                $user->update($data);

                // Gửi lại email kích hoạt với thông tin mới nhất
                Mail::to($data['email'])->send(new Authentication([
                    'fullname' => $user->fullname,
                    'verify_code' => $data['verify_code'],
                    'email' => $user->email,
                ]));

                DB::commit();
                return redirect()->intended(route('user.auth.oauth', ['code' => $data['code']]))->with('success', __('Đăng ký thành công. Vui lòng kiểm tra email để kích hoạt tài khoản.'));
            } else {
                $data['membership_id'] = 1;
                $user = $this->repository->create($data);
                $roles = $this->getRoleCustomer();
                $this->repository->assignRoles($user, [$roles]);

                if (env('IS_VERIFY_EMAIL')) {
                    // Gửi email kích hoạt
                    Mail::to($data['email'])->send(new Authentication($data));
                    DB::commit();
                    return redirect()->intended(route('user.auth.oauth', ['code' => $data['code']]))->with('success', __('Đăng ký thành công. Vui lòng kiểm tra email để kích hoạt tài khoản.'));
                }
                Auth::guard('web')->login($user);
                DB::commit();
                return redirect()->route('user.profile.indexUser')->with('success', __('Xác thực tài khoản thành công'));
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }


    public function oauth(OauthReqest $request)
    {
        return view('user.auth.oauth_verification', ['code' => $request->query('code')]);
    }

    public function verify(OauthReqest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $user = $this->repository->findByField('code', $data['code']);
            if ($user && $user['verify_code'] == $data['verify_code'] && $user->verify_code_expiration) {
                $this->repository->update($user['id'], [
                    'is_email_verified' => 1,
                    'verify_code' => null,
                    'verify_code_expiration' => null
                ]);
                Auth::guard('web')->login($user);
                DB::commit();
                return redirect()->route('user.profile.indexUser')->with('success', __('Xác thực tài khoản thành công'));
            }

            DB::commit();
            return back()->with('error', __('Mã OTP xác thực tài khoản không đúng hoặc đã hết hạn.'));
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }
}
