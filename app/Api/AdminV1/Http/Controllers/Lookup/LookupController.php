<?php

namespace App\Api\AdminV1\Http\Controllers\Lookup;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Admin\Repositories\Province\ProvinceRepositoryInterface;
use App\Admin\Repositories\Ward\WardRepositoryInterface;
use Illuminate\Http\JsonResponse;

class LookupController extends Controller
{
    protected $provinceRepository;
    protected $wardRepository;

    public function __construct(
        ProvinceRepositoryInterface $provinceRepository,
        WardRepositoryInterface $wardRepository
    ) {
        $this->provinceRepository = $provinceRepository;
        $this->wardRepository = $wardRepository;
    }

    public function provinces(): JsonResponse
    {
        $provinces = $this->provinceRepository->getQueryBuilder()->get();
        $data = $provinces->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
            ];
        });

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $data,
        ]);
    }

    public function wards(): JsonResponse
    {
        $wards = $this->wardRepository->getQueryBuilder()->get();
        $data = $wards->map(function ($item) {
            return [
                'id' => $item->id,
                'name' => $item->name,
                'province_id' => $item->province_id,
            ];
        });

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $data,
        ]);
    }
}
