<?php

namespace App\Admin\Services\Review;

use App\Admin\Services\Review\ReviewServiceInterface;
use App\Admin\Repositories\Review\ReviewRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use App\Api\V1\Repositories\Order\OrderRepositoryInterface;
use App\Enums\Order\OrderReview;
use App\Traits\UseLog;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReviewService implements ReviewServiceInterface
{
    use Setup, UseLog, AuthService;
    protected $data;
    protected $repository;
    protected $orderRepository;
    protected $userRepository;
    protected $fileService;

    public function __construct(
        ReviewRepositoryInterface $repository,
        OrderRepositoryInterface $orderRepository,
        UserRepositoryInterface $userRepository,
        FileService $fileService,
    ) {
        $this->repository = $repository;
        $this->orderRepository = $orderRepository;
        $this->userRepository = $userRepository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        DB::beginTransaction();
        try {
            if ($request->routeIs('api.v1.review.store')) {

                $this->data['user_id'] = $this->getCurrentUserId();
                if (isset($this->data['images'])) {
                    $this->data['images'] = $this->fileService->uploadFileBase64($this->data['images']);
                }

                // Lấy product_id từ order_detail
                if (isset($this->data['order_detail_id'])) {
                    $orderDetail = \App\Models\OrderDetail::find($this->data['order_detail_id']);
                    if ($orderDetail) {
                        $this->data['product_id'] = $orderDetail->product_id;
                        $this->data['order_id'] = $orderDetail->order_id;
                    }
                }
            }

            $instance = $this->repository->create($this->data);

            // Cập nhật is_reviewed trong order_details
            if (isset($this->data['order_detail_id'])) {
                \App\Models\OrderDetail::where('id', $this->data['order_detail_id'])
                    ->update(['is_reviewed' => 1]);
            }

            DB::commit();
            return $instance;
        } catch (Exception $e) {
            $this->logError('Failed to store review: ', $e);
            DB::rollBack();
            return false;
        }
    }

    public function update(Request $request)
    {
        $this->data = $request->validated();

        DB::beginTransaction();
        try {
            $instance = $this->repository->update($this->data['id'], $this->data);
            DB::commit();
            return $instance;
        } catch (Exception $e) {

            $this->logError('Failed to update review: ', $e);
            DB::rollBack();
            return false;
        }
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }
}
