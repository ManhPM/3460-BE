<?php

namespace App\Api\AdminV1\Http\Controllers\Setting;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Setting\UpdateSettingRequest;
use App\Api\AdminV1\Repositories\Setting\SettingRepositoryInterface;
use App\Api\AdminV1\Services\Setting\SettingService;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        SettingRepositoryInterface $repository,
        SettingService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    /**
     * Get all settings
     */
    public function index(Request $request)
    {
        $settings = $this->repository->getAllWithDetails();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $settings,
        ]);
    }

    /**
     * Update settings
     */
    public function update(UpdateSettingRequest $request)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) {
                return $this->service->update($request->validated());
            },
            __('setting.updated_success')
        );
    }
}
