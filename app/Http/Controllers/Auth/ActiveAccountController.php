<?php

namespace App\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Http\Request;

class ActiveAccountController extends Controller
{
    //
    public function __construct(
        UserRepositoryInterface $repository,
    ) {
        parent::__construct();
        $this->repository = $repository;
    }

    public function index(Request $request)
    {
        $token = $request->input('token');
        $code = $request->input('code');
        $user = User::where('verify_code', $token)
            ->where('code', $code)
            ->first();

        if (!$user) {
            return response()->json(['message' => 'Token hoặc mã không hợp lệ.'], 404);
        }

        User::where('email', $user->email)
            ->where('id', '!=', $user->id)
            ->delete();

        $user->is_email_verified = 1;
        $user->verify_code = null;
        $user->save();

        return view('admin.auth.account.success');
    }
}
