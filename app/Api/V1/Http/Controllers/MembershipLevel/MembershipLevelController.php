<?php

namespace App\Api\V1\Http\Controllers\MembershipLevel;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Api\V1\Http\Resources\MembershipLevel\MembershipLevelResource;

/**
 * @group Hạng thành viên
 */
class MembershipLevelController extends Controller
{
    protected $repository;

    public function __construct(MembershipLevelRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Danh sách hạng thành viên
     *
     * Lấy danh sách tất cả các hạng thành viên có sẵn.
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 1,
     *              "name": "Thành viên Đồng",
     *              "min_points": 0,
     *              "discount_percentage": 2,
     *              "color_1": "#FCEBDD",
     *              "color_2": "#EA7C2D",
     *              "color_3": "#F3CBAA",
     *              "icon": "icon_url",
     *              "description": "<p>Mô tả HTML</p>"
     *          }
     *      ]
     * }
     */
    public function index()
    {
        $membershipLevels = $this->repository->getQueryBuilder()
            ->orderBy('min_points', 'asc')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => MembershipLevelResource::collection($membershipLevels)
        ]);
    }
}
