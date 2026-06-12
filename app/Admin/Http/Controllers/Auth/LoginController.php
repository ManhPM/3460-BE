<?php

namespace App\Admin\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Auth\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    private $login;
    public function getView()
    {
        return [
            'index' => 'admin.auth.login',
            'indexUser' => 'user.auth.login',
            'login' => 'user.auth.login'
        ];
    }

    public function index()
    {
        return view($this->view['index']);
    }

    public function notfound()
    {
        return view('admin.not-found');
    }

    public function indexUser()
    {
        return view($this->view['indexUser']);
    }

    public function forgotPassword()

    {
        return view($this->view['forgot-password']);
    }

    public function login(LoginRequest $request)
    {
        $this->login = $request->validated();

        Auth::guard('web')->logout();

        // Authenticate and regenerate session
        if ($this->resolveAdmin()) {
            $request->session()->regenerate();
            return $this->handleAdminLogin();
        }

        return back()->with('error', __('Tên đăng nhập hoặc mật khẩu không đúng'));
    }

    protected function handleUserLogin()
    {
        if (Auth::guard('web')->check()) {
            return redirect()->intended(route('user.profile.indexUser'))->with('success', __('Đăng nhập thành công'));
        }
    }

    protected function handleAdminLogin()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.dashboard'))->with('success', __('Đăng nhập thành công'));
        }
    }

    protected function resolveAdmin()
    {
        return Auth::guard('admin')->attempt($this->login, true);
    }

    protected function resolveWeb()
    {
        return Auth::guard('web')->attempt($this->login, true);
    }
}
