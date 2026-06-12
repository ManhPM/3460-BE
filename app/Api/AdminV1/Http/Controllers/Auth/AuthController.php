<?php

namespace App\Api\AdminV1\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Auth\LoginRequest;
use App\Api\AdminV1\Http\Requests\Auth\RefreshTokenRequest;
use App\Api\AdminV1\Http\Requests\Auth\ChangePasswordRequest;
use App\Api\AdminV1\Http\Requests\Auth\ProfileRequest;
use App\Api\AdminV1\Http\Resources\Auth\AdminResource;
use App\Admin\Services\File\FileService;
use App\Traits\JwtService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Admin;

class AuthController extends Controller
{
    use JwtService;

    protected $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    /**
     * Login admin user
     */
    public function login(LoginRequest $request)
    {
        return $this->loginAdmin($request, 'admin-api');
    }

    /**
     * Refresh token
     */
    public function refresh(RefreshTokenRequest $request)
    {
        return $this->refreshToken($request);
    }

    /**
     * Logout admin user
     */
    public function logout(Request $request)
    {
        try {
            // Invalidate the token (add it to blacklist)
            // JWT middleware sẽ tự động lấy token từ header Authorization
            JWTAuth::parseToken()->invalidate();
        } catch (\Exception $e) {
            // Token already invalid or not provided
        }

        return response()->json([
            'status' => 200,
            'message' => 'Đăng xuất thành công'
        ]);
    }

    /**
     * Get authenticated admin profile
     */
    public function profile(Request $request)
    {
        $admin = Auth::guard('admin-api')->user();

        return response()->json([
            'status' => 200,
            'message' => 'Thực hiện thành công',
            'data' => new AdminResource($admin)
        ]);
    }

    /**
     * Update authenticated admin profile
     */
    public function updateProfile(ProfileRequest $request)
    {
        $admin = Auth::guard('admin-api')->user();
        $data = $request->validated();

        if (isset($data['avatar'])) {
            $avatar = $this->fileService->uploadSingleFileBase64($data['avatar']);
            $data['avatar'] = $avatar;
        }

        // Map 'name' to 'fullname' if needed
        if (isset($data['name'])) {
            $data['fullname'] = $data['name'];
            unset($data['name']);
        }

        // Update admin
        $admin->update($data);

        return response()->json([
            'status' => 200,
            'message' => __('auth.profile_updated_success'),
            'data' => new AdminResource($admin->fresh())
        ]);
    }

    /**
     * Change password
     */
    public function changePassword(ChangePasswordRequest $request)
    {
        $data = $request->all();
        $admin = Auth::guard('admin-api')->user();

        // Check if using 'current_password' or 'old_password'
        $currentPassword = $data['current_password'] ?? $data['old_password'] ?? null;
        $newPassword = $data['password'] ?? $data['new_password'] ?? null;

        if (!$currentPassword) {
            return response()->json([
                'status' => 400,
                'message' => __('please_enter_old_password')
            ], 400);
        }

        if (!$newPassword) {
            return response()->json([
                'status' => 400,
                'message' => __('please_enter_new_password')
            ], 400);
        }

        if (!Hash::check($currentPassword, $admin->password)) {
            return response()->json([
                'status' => 400,
                'message' => 'Mật khẩu hiện tại không đúng'
            ], 400);
        }

        // Check password confirmation if provided
        if (isset($data['password_confirmation']) && $newPassword !== $data['password_confirmation']) {
            return response()->json([
                'status' => 400,
                'message' => 'Mật khẩu mới và xác nhận mật khẩu không khớp'
            ], 400);
        }

        $admin->update([
            'password' => Hash::make($newPassword)
        ]);

        return response()->json([
            'status' => 200,
            'message' => 'Đổi mật khẩu thành công'
        ]);
    }
}
