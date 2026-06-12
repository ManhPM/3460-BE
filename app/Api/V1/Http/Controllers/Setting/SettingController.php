<?php

namespace App\Api\V1\Http\Controllers\Setting;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Setting\SettingRepositoryInterface;
use App\Api\V1\Http\Resources\Setting\SettingResource;
use App\Enums\Setting\SettingGroup;
use Illuminate\Http\JsonResponse;

/**
 * @group Cài đặt
 */
class SettingController extends Controller
{
    public function __construct(
        SettingRepositoryInterface $repository
    ) {
        $this->repository = $repository;
    }

    /**
     * Thông tin cài đặt
     *
     * Lấy tất cả thông tin cài đặt chung (Facebook, Zalo, Số điện thoại, Email, Về chúng tôi, Chính sách, Điều khoản).
     *
     * @headersParam X-TOKEN-ACCESS string
     * token để lấy dữ liệu. Example: ijCCtggxLEkG3Yg8hNKZJvMM4EA1Rw4VjVvyIOb7
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": {
     *          "facebook_url": "https://www.facebook.com/mevivu",
     *          "zalo_url": "https://zalo.me/0909090909",
     *          "phone_number_1": "0909090909",
     *          "phone_number_2": "0909090909",
     *          "email": "info@mevivu.com",
     *          "about_us": "Nội dung về chúng tôi",
     *          "policy": "Nội dung chính sách",
     *          "term": "Nội dung điều khoản"
     *      }
     * }
     */
    public function index(): JsonResponse
    {
        // Lấy các settings từ group General
        $settings = $this->repository->getByGroup([SettingGroup::General]);

        // Lọc các settings cần thiết
        $settingKeys = [
            'facebook_url',
            'zalo_url',
            'phone_number_1',
            'phone_number_2',
            'email',
            'about_us',
            'policy',
            'term',
            'address_office',
            'amount_to_exchange',
            'exchange_percent',
            'bank_transfer_info'
        ];

        $filteredSettings = $settings->whereIn('setting_key', $settingKeys);

        // Chuyển đổi thành array key-value
        $data = [];
        foreach ($filteredSettings as $setting) {
            $data[$setting->setting_key] = $setting->plain_value;
        }

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $data
        ]);
    }


    public function point(): JsonResponse
    {
        // Lấy các settings từ group General
        $settings = $this->repository->getByGroup([SettingGroup::Config]);

        // Chuyển đổi thành array key-value
        $data = [];
        foreach ($settings as $setting) {
            $data[$setting->setting_key] = $setting->plain_value;
        }

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $data
        ]);
    }
}
