<?php

namespace App\Api\V1\Http\Controllers\Auth;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\Setup;
use App\Api\V1\Http\Requests\Auth\{RegisterRequest, LoginRequest, ResendOTPRequest, UpdateRequest, UpdatePasswordRequest, VerifyEmailRequest, VerifyPhoneRequest};
use App\Api\V1\Http\Requests\CommissionWithdrawal\CommissionWithdrawalRequest;
use App\Api\V1\Repositories\User\UserRepositoryInterface;
use App\Api\V1\Services\Auth\AuthServiceInterface;
use Illuminate\Support\Facades\Auth;
use \Illuminate\Http\Request;
use App\Api\V1\Http\Resources\Auth\AuthResource;
use App\Api\V1\Http\Resources\Auth\MembershipLevelResource;
use App\Api\V1\Http\Resources\Auth\WithdrawHistoryResource;
use App\Api\V1\Http\Resources\Voucher\AllVoucherResource;
use App\Api\V1\Support\Response;
use App\Mail\Authentication;
use App\Traits\JwtService;
use App\Traits\Membership;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

/**
 * @group Người dùng
 */

class AuthController extends Controller
{
    use Setup, Response, Membership, JwtService;
    private $login;
    private $fileService;
    protected $orderDetailRepository;
    protected $commissionWithdrawalRepository;
    protected $settingRepository;
    protected $voucherRepository;
    protected $membershipLevelRepository;
    public function __construct(
        UserRepositoryInterface $repository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        CommissionWithdrawalRepositoryInterface $commissionWithdrawalRepository,
        AuthServiceInterface $service,
        FileService $fileService,
        SettingRepositoryInterface $settingRepository,
        VoucherRepositoryInterface $voucherRepository,
        MembershipLevelRepositoryInterface $membershipLevelRepository,
    ) {
        $this->repository = $repository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->commissionWithdrawalRepository = $commissionWithdrawalRepository;
        $this->service = $service;
        $this->fileService = $fileService;
        $this->settingRepository = $settingRepository;
        $this->voucherRepository = $voucherRepository;
        $this->membershipLevelRepository = $membershipLevelRepository;
    }
    /**
     * Xác thực tài khoản
     *
     * Dùng để xác thực tài khoản khi đăng ký kích hoạt tài khoản hoặc khi quên mật khẩu.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam email string required
     * Email Của bạn. Example: example@gmail.com
     *
     * @bodyParam verify_code string required
     * Mã OTP xác thực tài khoản. Example: 1234
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
    public function verifyEmail(VerifyEmailRequest $request)
    {
        $user = $this->repository->findByField('email', $request->input('email'));
        if ($user && $user->verify_code == $request->input('verify_code') && $user->verify_code_expiration > Carbon::now()) {
            $user->update([
                'verify_code' => null,
                'verify_code_expiration' => null,
                'is_email_verified' => 1,
            ]);
            return response()->json([
                'status' => 200,
                'message' => __('success')
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => __('auth.verification_code_invalid_or_expired')
        ], 400);
    }

    /**
     * Cập nhật hạng thành viên
     *
     * Cập nhật hạng thành viên bằng cách bấm nút thủ công sẽ gọi api này dành cho người dùng muốn tự cập nhật hạng của bản thân.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "membership_level": [
     *              {
     *                  "id": 1,
     *                  "name": "Thành viên",
     *                  "min_points": 0,
     *                  "color_1": "#a1f7a4",
     *                  "color_2": "#34f95b"
     *              },
     *              {
     *                  "id": 2,
     *                  "name": "Bạc",
     *                  "min_points": 30000,
     *                  "color_1": "#C0C0C0",
     *                  "color_2": "#E8E8E8"
     *              },
     *              {
     *                  "id": 3,
     *                  "name": "Vàng",
     *                  "min_points": 40000,
     *                  "color_1": "#ffd700",
     *                  "color_2": "#ffd738"
     *              },
     *              {
     *                  "id": 4,
     *                  "name": "Kim cương",
     *                  "min_points": 50000,
     *                  "color_1": "#a9f5f2",
     *                  "color_2": "#4dfef8"
     *              }
     *          ],
     *          "member": {
     *              "id": 4,
     *              "name": "Kim cương",
     *              "min_points": 50000,
     *              "color_1": "#a9f5f2",
     *              "color_2": "#4dfef8",
     *              "icon": "ti ti-diamond"
     *          }
     *      }
     * }
     */
    public function updateMembership(Request $request)
    {
        $user = $request->user();
        $this->updateMembershipLevel($user);
        $membershipLevels = $this->membershipLevelRepository->getAll();
        return $this->jsonResponseSuccess([
            'membership_level' => $membershipLevels->map(fn($item) => new MembershipLevelResource($item)),
            'member' => $user->member,
        ]);
    }
    /**
     * Lấy thông tin user
     *
     * Lấy user hiện tại thông qua access_token. Trong đó có:
     * <ul>
     * <li><strong>gender</strong>:
     *      <ul>
     *          <li>1: Nam</li>
     *          <li>2: Nữ</li>
     *          <li>3: Khác</li>
     *      </ul>
     * </li>
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "membership_level": [
     *              {
     *                  "id": 1,
     *                  "name": "Thành viên",
     *                  "min_points": 0,
     *                  "color_1": "#a1f7a4",
     *                  "color_2": "#34f95b"
     *              },
     *              {
     *                  "id": 2,
     *                  "name": "Bạc",
     *                  "min_points": 30000,
     *                  "color_1": "#C0C0C0",
     *                  "color_2": "#E8E8E8"
     *              },
     *              {
     *                  "id": 3,
     *                  "name": "Vàng",
     *                  "min_points": 40000,
     *                  "color_1": "#ffd700",
     *                  "color_2": "#ffd738"
     *              },
     *              {
     *                  "id": 4,
     *                  "name": "Kim cương",
     *                  "min_points": 50000,
     *                  "color_1": "#a9f5f2",
     *                  "color_2": "#4dfef8"
     *              }
     *          ],
     *          "member": {
     *              "id": 4,
     *              "name": "Kim cương",
     *              "min_points": 50000,
     *              "color_1": "#a9f5f2",
     *              "color_2": "#4dfef8",
     *              "icon": "ti ti-diamond"
     *          },
     *          "id": 1,
     *          "avatar": "http://localhost:8080/CoreBanHang/public/miniapp/UCF67C1724127911.png",
     *          "fullname": "NGUYỄN PHÚC NHÂN",
     *          "email": "marispham1509@gmail.com",
     *          "birthday": "1995-01-01",
     *          "gender": 2,
     *          "points": 420000,
     *          "bank_name": "VIETCOMBANK",
     *          "bank_account": "NGUYEN PHUC NHAN",
     *          "bank_account_number": "123123123123123",
     *          "affiliate_code": "AF141735266173",
     *          "balance": 0,
     *          "exchange_percent": "5",
     *          "amount_to_exchange": "100",
     *          "address": {
     *              "id": 72,
     *              "name": "PHAM MANH MINH",
     *              "phone": "0961592551",
     *              "address": "D2/084B Ấp Nam Sơn",
     *              "province": {
     *                  "id": 4,
     *                  "name": "Tỉnh Bắc Kạn"
     *              },
     *              "ward": {
     *                  "id": 956,
     *                  "name": "Xã Cao Thượng"
     *              },
     *              "is_default": 1
     *          }
     *      }
     * }
     */
    public function show(Request $request)
    {
        $user = $request->user();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AuthResource($user)
        ]);
    }
    /**
     * Đăng ký
     *
     * Tạo mới 1 user.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam fullname string required
     * Họ và tên của bạn. Example: Nguyen Van A
     *
     * @bodyParam phone string required
     * Số điện thoại của bạn. Example: 0999999999
     *
     * @bodyParam email string required
     * Email Của bạn. Example: example@gmail.com
     *
     * @bodyParam password string required
     * Mật khẩu của bạn. Example: 123456
     *
     * @bodyParam password_confirmation string required
     * Nhập lại mật khẩu của bạn. Example: 123456
     *
     * @bodyParam bank_name string nullable
     * Tên ngân hàng thụ hưởng hoa hồng. Example: VIETCOMBANK
     *
     * @bodyParam bank_account_number string nullable
     * Số tài khoản thủ hưởng hoa hồng. Example: 112233445566
     *
     * @bodyParam bank_account string nullable
     * Tên chủ tài khoản thụ hưởng hoa hồng. Example: PHAM MINH MANH
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện không thành công."
     * }
     */
    public function register(RegisterRequest $request)
    {
        $instance = $this->service->store($request);
        if ($instance) {
            return response()->json([
                'status' => 200,
                'message' => __('auth.register_success')
            ]);
        }
        return response()->json([
            'status' => 400,
            'message' => __('fail')
        ], 400);
    }

    protected function resolve($username)
    {
        if ($username == 'email') {
            return Auth::attempt(
                [
                    'email' => $this->login['username'],
                    'password' => $this->login['password'],
                ],
            );
        } else {
            return Auth::attempt(
                [
                    'phone' => $this->login['username'],
                    'password' => $this->login['password'],
                ],
            );
        }
    }

    /**
     * Gửi yêu cầu rút tiền hoa hồng
     *
     * Gửi yêu cầu rút tiền hoa hồng trong tài khoản.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam amount integer required
     * Số tiền muốn rút. Example: 123456
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     * @response 400 {
     *      "status": 400,
     *      "message": "Thực hiện thất bại."
     * }
     *
     */
    public function withdraw(CommissionWithdrawalRequest $request)
    {
        $result = $this->service->withdraw($request);
        if ($result) {
            return response()->json([
                'status' => 200,
                'message' => __('success')
            ]);
        }
        return response()->json([
            'status' => 500,
            'message' =>  __('fail')
        ], 500);
    }

    /**
     * Lịch sử rút hoa hồng
     *
     * Lấy lịch sử rút hoa hồng của người dùng
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     * "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 17,
     *              "user": "KhanhTranQuoc",
     *              "amount": 900000,
     *              "status": "Đang chờ",
     *              "bank_account_number": "0421000488622",
     *              "processed_at": null
     *          }
     *      ]
     * }
     *
     */
    public function withdrawHistory(Request $request)
    {
        $user = $request->user();
        $items = $this->commissionWithdrawalRepository->withdrawHistory($user->id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new WithdrawHistoryResource($items)
        ]);
    }

    /**
     * Đăng nhập
     *
     * Đăng nhập tài khoản.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam email string required
     * Tên tài khoản là số marispham1509@gmail.com. Example: 0999999999
     *
     * @bodyParam password string required
     * Mật khẩu của bạn. Example: 123456
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Đăng nhập thành công.",
     *      "access_token": "1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K"
     * }
     * @response 401 {
     *      "status": 401,
     *      "message": "Tài khoản hoặc mật khẩu không đúng."
     * }
     *
     */
    public function login(LoginRequest $request)
    {
        return $this->loginUser($request, 'user');
    }
    /**
     * Cập nhật thông tin người dùng
     *
     * Cập nhật thông tin user hiện tại.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @bodyParam fullname string
     * Họ và tên. Example: Nguyen Van A
     *
     * @bodyParam email string
     * Email. Example: example@gmail.com
     *
     * @bodyParam gender integer
     * Giới tính. Example: 1
     *
     * @bodyParam phone string
     * Số điện thoại. Example: 0961592551
     *
     * @bodyParam bank_name string
     * Tên ngân hàng. Example: VIETCOMBANK
     *
     * @bodyParam bank_account string
     * Tên chủ tài khoản. Example: NGUYEN PHUC NHAN
     *
     * @bodyParam bank_account_number string
     * Số tài khoản. Example: 123123123123
     *
     * @bodyParam province_id integer
     * ID Tỉnh thành. Example: 1
     *
     * @bodyParam ward_id integer
     * ID Phường xã. Example: 1
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *           "id": 2,
     *           "username": "0999999999",
     *           "fullname": "Truong",
     *           "email": "truog@gmai1l.com",
     *           "gender": "Nam",
     *           "points": 437000,
     *           "bank_name": "VIETCOMBANK",
     *           "bank_account": "NGUYEN PHUC NHAN",
     *           "bank_account_number": "123123123123123",
     *           "affiliate_code": "AF981733846484",
     *           "balance": 450000
     *      }
     * }
     */
    public function update(UpdateRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        if (isset($data['email']) && $data['email'] !== $user->email) {
            $data['is_email_verified'] = 0; // Đặt lại trạng thái is_email_verified
            $data['verify_code'] = random_int(1000, 9999); // Tạo token mới
            $data['verify_code_expiration'] = Carbon::now()->addMinutes(30);

            // Gửi email kích hoạt tài khoản
            Mail::to($data['email'])->send(new Authentication([
                'fullname' => $user->fullname,
                'verify_code' => $data['verify_code'],
                'email' => $data['email'], // Email mới
            ]));
        }

        if (isset($data['avatar'])) {
            $avatar = $data['avatar'];
            $data['avatar'] = $this->fileService->uploadSingleFileBase64($avatar);
        }

        $user->update($data);

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new AuthResource($user)
        ], 200);
    }

    /**
     * Cập nhật mật khẩu
     *
     * Cập nhật mật khẩu user hiện tại.
     *
     * @bodyParam old_password string required
     * Mật khẩu cũ của bạn. Example: 123
     *
     * @bodyParam password string required
     * Mật khẩu của bạn. Example: 123456
     *
     * @bodyParam password_confirmation string required
     * Nhập lại mật khẩu của bạn. Example: 123456
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $password = bcrypt($request->input('password'));
        $user = $request->user();
        $user->update([
            'password' => $password
        ]);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
        ], 200);
    }

    /**
     * Xóa tài khoản
     *
     * Thực hiện xóa tài khoản.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @bodyParam email string required
     * Tài khoản. Example: marispham1509@gmail.com
     *
     * @bodyParam password string required
     * Mật khẩu của bạn. Example: 123456
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     *
     * @response 500 {
     *      "status": 500,
     *      "message": "Xóa tài khoản thất bại."
     * }
     *
     * @param  App\Api\V1\Http\Requests\Auth\UpdatePasswordRequest  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function delete(LoginRequest $request)
    {
        $this->login = $request->validated();
        if (Auth::attempt($this->login)) {
            if ($request->user()->is_email_verified) {
                // Cập nhật trạng thái tài khoản người dùng
                $this->repository->update($request->user()->id, ['is_email_verified' => 0]);

                // Xóa tất cả các token liên quan đến người dùng này
                DB::table('personal_access_tokens')
                    ->where('tokenable_id', $request->user()->id)
                    ->delete();

                return response()->json([
                    'status' => 200,
                    'message' => __('auth.delete_account_success'),
                ], 200);
            }
        }
        return response()->json([
            'status' => 500,
            'message' => __('auth.delete_account_failed')
        ], 500);
    }

    /**
     * Đăng xuất
     *
     * ? Đăng xuất Khách hàng hiện tại.
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @responseFile 200 App/Api/V1/Http/Resources/BaseResource.json
     */
    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => 200,
            'message' => __('logout_success')
        ]);
    }

    /**
     * Gửi lại mã xác minh
     *
     * Gửi lại mã xác minh khi người dùng chưa nhận được mã hoặc không nhập mà mã hết hạn.
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
    public function resendOTP(ResendOTPRequest $request)
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
     * Xác thực số điện thoại người dùng
     *
     * Dùng để xác thực số điện thoại người dùng dưới BE khi trên FE đã xác thực thành công.
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công."
     * }
     */
    public function verifyPhone(VerifyPhoneRequest $request): JsonResponse
    {
        // $users = $this->repository->getBy(['phone' => $request->input('phone')]);
        // foreach ($users as $user) {
        //     if ($user->id != auth()->id()) {
        //         $user->update([
        //             'is_phone_verified' => 0,
        //         ]);
        //     } else {
        //         $user->update([
        //             'is_phone_verified' => 1,
        //         ]);
        //     }
        // }

        return $this->jsonResponseSuccess(null);
    }

    /**
     * Danh sách voucher của khách hàng
     *
     * Lấy danh sách voucher của khách hàng.
     *
     * @queryParam voucher_type integer
     * Loại voucher cần lọc (1: Giảm giá tiền hàng, 2: Giảm giá vận chuyển). Example: 1
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @authenticated Authorization string required
     * access_token được cấp sau khi đăng nhập. Example: Bearer 1|WhUre3Td7hThZ8sNhivpt7YYSxJBWk17rdndVO8K
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 1,
     *              "code": "VOUCHER1",
     *              "date_end": "25-01-2025 16:28",
     *              "type": "Phần trăm",
     *              "voucher_type": "Giảm giá tiền hàng",
     *              "min_order_amount": 400000,
     *              "max_discount_value": 50000,
     *              "discount_value": 20
     *          },
     *          {
     *              "id": 2,
     *              "code": "VOUCHER1",
     *              "date_end": "25-01-2025 16:28",
     *              "type": "Phần trăm",
     *              "voucher_type": "Giảm giá vận chuyển",
     *              "min_order_amount": 400000,
     *              "max_discount_value": 50000,
     *              "discount_value": 20
     *          }
     *      ]
     * }
     */
    public function voucher(Request $request): JsonResponse
    {
        return $this->jsonResponseSuccess(new AllVoucherResource($this->voucherRepository->getValidForUser($request->input('voucher_type', null))));
    }
}
