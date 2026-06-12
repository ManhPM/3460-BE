<?php

namespace App\Api\AdminV1\Http\Controllers\Review;

use App\Api\AdminV1\Http\Controllers\Controller;
use App\Api\AdminV1\Http\Requests\Review\ReviewRequest;
use App\Api\AdminV1\Http\Resources\Review\ReviewResource;
use App\Api\AdminV1\Http\Resources\Review\ReviewCollection;
use App\Api\AdminV1\Repositories\Review\ReviewRepositoryInterface;
use App\Api\AdminV1\Services\Review\ReviewService;

class ReviewController extends Controller
{
    protected $repository;
    protected $service;

    public function __construct(
        ReviewRepositoryInterface $repository,
        ReviewService $service
    ) {
        $this->repository = $repository;
        $this->service = $service;
    }

    public function index()
    {
        $reviews = $this->repository->getFiltered();
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new ReviewCollection($reviews),
        ]);
    }

    public function show(int $id)
    {
        $review = $this->repository->findOrFail($id);
        $review->load(['user', 'product']);
        return response()->json([
            'status' => 200,
            'message' => __('success'),
            'data' => new ReviewResource($review)
        ]);
    }

    public function destroy(int $id)
    {
        return $this->handleDeleteResponse(
            $id,
            function ($id) {
                return $this->service->delete($id);
            },
            __('review.deleted_success')
        );
    }

    public function reply(ReviewRequest $request, int $id)
    {
        return $this->handleUpdateResponse(
            $request,
            function ($request) use ($id) {
                $review = $this->service->reply($id, $request->validated()['admin_reply'] ?? $request->input('admin_reply'));
                return new ReviewResource($review);
            },
            __('review.replied_success')
        );
    }

    public function approve(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $review = $this->service->approve($id);
                return new ReviewResource($review);
            },
            __('review.approved_success')
        );
    }

    public function reject(int $id)
    {
        return $this->handleResponse(
            function () use ($id) {
                $review = $this->service->reject($id);
                return new ReviewResource($review);
            },
            __('review.rejected_success')
        );
    }
}
