<?php

namespace App\Traits;

use App\Api\V1\Http\Resources\Auth\AuthResource;
use App\Api\AdminV1\Http\Resources\Auth\AdminResource;
use App\Models\User;
use App\Models\Admin;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use App\Traits\NotifiesViaFirebase;

trait JwtService
{
    use NotifiesViaFirebase;

    protected function respondWithToken($token, $refreshToken, $user): JsonResponse
    {
        return response()->json([
            'status' => 200,
            'data' => [
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'user' => new AuthResource($user),
            ],
            'message' => __('login_success'),
        ]);
    }

    protected function respondWithAdminToken($token, $refreshToken, $admin): JsonResponse
    {
        $ttl = config('jwt.ttl', 60); // minutes

        return response()->json([
            'status' => 200,
            'message' => 'Đăng nhập thành công',
            'data' => [
                'access_token' => $token,
                'refresh_token' => $refreshToken,
                'user' => new AdminResource($admin),
                'token_type' => 'bearer',
                'expires_in' => $ttl * 60, // seconds
            ]
        ]);
    }

    private function createRefreshToken($user, $guard)
    {
        $now = time();

        $data = [
            'sub' => $user->getKey(), // claim bắt buộc
            'iat' => $now,            // thời điểm tạo
            'exp' => $now + config('jwt.refresh_ttl', 20160) * 60, // TTL mặc định phút → giây
            'nbf' => $now,
            'jti' => uniqid(),
            'user_id' => $user->id,
            'guard' => $guard,
            'random' => rand() . $now,
            'is_refresh_token' => true,
        ];

        return JWTAuth::getJWTProvider()->encode($data);
    }


    /**
     * Login user based on guard type
     */
    public function loginUser(Request $request, string $guard): JsonResponse
    {
        $credentials = $request->validated();
        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        // Tìm user theo email hoặc phone
        $user = User::where('email', $username)
            ->orWhere('phone', $username)
            ->first();

        if (!$user) {
            return response()->json([
                'status' => 400,
                'message' => __('username_or_password_incorrect'),
            ], 400);
        }
        // Kiểm tra xác thực email hoặc phone
        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);

        if ($isEmail && !$user->is_email_verified) {
            return response()->json([
                'status' => 400,
                'message' => __('account_not_activated'),
            ], 400);
        }

        if (!$isEmail && !$user->is_phone_verified) {
            return response()->json([
                'status' => 400,
                'message' => __('account_not_activated'),
            ], 400);
        }

        // Thử đăng nhập với email hoặc phone
        $loginCredentials = $isEmail
            ? ['email' => $username, 'password' => $password]
            : ['phone' => $username, 'password' => $password];

        if ($token = Auth::guard($guard)->attempt($loginCredentials)) {
            $user = Auth::guard($guard)->user();

            // Tạo refresh token mới
            $refreshToken = $this->createRefreshToken($user, $guard);

            return $this->respondWithToken($token, $refreshToken, $user);
        }

        return response()->json([
            'status' => 400,
            'message' => __('username_or_password_incorrect'),
        ], 400);
    }

    /**
     * Refresh token with guard awareness
     */
    public function refreshToken(Request $request): JsonResponse
    {
        $data = $request->validated();
        $refreshToken = $data['refresh_token'];

        try {
            $decoded = JWTAuth::setToken($refreshToken)->getPayload();

            if (!$decoded->get('is_refresh_token', false)) {
                return response()->json(['message' => 'Invalid token type.'], 400);
            }

            $userId = $decoded->get('user_id');
            $guard = $decoded->get('guard');

            // Tìm user hoặc admin dựa vào guard
            if ($guard === 'admin-api') {
                $admin = Admin::find($userId);
                if (!$admin) {
                    return response()->json(['message' => 'Admin not found.'], 404);
                }
                $newToken = Auth::guard($guard)->login($admin);
                $newRefreshToken = $this->createRefreshToken($admin, $guard);
                return $this->respondWithAdminToken($newToken, $newRefreshToken, $admin);
            } else {
                $user = User::find($userId);
                if (!$user) {
                    return response()->json(['message' => 'User not found.'], 404);
                }
                $newToken = Auth::guard($guard)->login($user);
                $newRefreshToken = $this->createRefreshToken($user, $guard);
                return $this->respondWithToken($newToken, $newRefreshToken, $user);
            }
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Invalid token.',
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Login admin based on guard type
     */
    public function loginAdmin(Request $request, string $guard = 'admin-api'): JsonResponse
    {
        $data = $request->validated();
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;

        if (!$email || !$password) {
            return response()->json([
                'status' => 400,
                'message' => __('email_and_password_required')
            ], 400);
        }

        // Tìm admin theo email
        $admin = Admin::where('email', $email)->first();

        if (!$admin) {
            return response()->json([
                'status' => 401,
                'message' => 'Email hoặc mật khẩu không đúng'
            ], 401);
        }

        // Thử đăng nhập với guard
        $loginCredentials = ['email' => $email, 'password' => $password];

        if ($token = Auth::guard($guard)->attempt($loginCredentials)) {
            $admin = Auth::guard($guard)->user();

            // Tạo refresh token mới
            $refreshToken = $this->createRefreshToken($admin, $guard);

            return $this->respondWithAdminToken($token, $refreshToken, $admin);
        }

        return response()->json([
            'status' => 401,
            'message' => 'Email hoặc mật khẩu không đúng'
        ], 401);
    }
}
