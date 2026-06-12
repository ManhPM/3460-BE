<?php

namespace App\Api\AdminV1\Http\Controllers\Section;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Section\SectionRequest;
use App\Admin\Repositories\Section\SectionRepositoryInterface;
use App\Admin\Services\Section\SectionServiceInterface;

class SectionController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        SectionRepositoryInterface $repository,
        SectionServiceInterface $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $sections = $this->repository->getFiltered();

        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $sections,
        ]);
    }

    public function show(int $id)
    {
        $section = $this->repository->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => $section->load('categories')
        ]);
    }

    public function store(SectionRequest $request)
    {
        return $this->handleStoreResponse(
            $request,
            function ($request) {
                $section = $this->service->store($request);
                return $section->load('categories');
            },
            __('section.created_success'),
            201
        );
    }

    public function update(SectionRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $request->merge(['id' => $id]);
                $section = $this->service->update($request);
                return $section->load('categories');
            },
            __('section.updated_success')
        );
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('section.deleted_success')
        );
    }
}
