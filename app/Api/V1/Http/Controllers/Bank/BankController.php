<?php

namespace App\Api\V1\Http\Controllers\Bank;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Api\V1\Http\Resources\Bank\BankResource;

/**
 * @group Ngân hàng
 */
class BankController extends Controller
{
    protected $repository;

    public function __construct(BankRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Danh sách ngân hàng
     *
     * Lấy danh sách ngân hàng có sẵn (có số tài khoản và đang hoạt động).
     *
     * @response 200 {
     *      "status": 200,
     *      "message": "Thực hiện thành công.",
     *      "data": [
     *          {
     *              "id": 64,
     *              "name": "Ngân hàng TMCP Công thương Việt Nam",
     *              "code": "ICB",
     *              "short_name": "VietinBank",
     *              "logo": "https://api.vietqr.io/img/ICB.png",
     *              "bank_account": "BUI THE VUU",
     *              "bank_account_number": "033529921113"
     *          }
     *      ]
     * }
     */
    public function index()
    {
        $banks = $this->repository->getQueryBuilder()
            ->where('is_active', 1)
            ->whereNotNull('bank_account_number')
            ->where('bank_account_number', '!=', '')
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new BankResource($banks)
        ]);
    }

    public function list()
    {
        $banks = $this->repository->getQueryBuilder()
            ->orderBy('id', 'desc')
            ->distinct('code')
            ->get();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new BankResource($banks)
        ]);
    }
}
