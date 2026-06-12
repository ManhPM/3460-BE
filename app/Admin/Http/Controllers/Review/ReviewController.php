<?php

namespace App\Admin\Http\Controllers\Review;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\Review\ReviewRepositoryInterface;
use App\Admin\Services\Review\ReviewServiceInterface;
use App\Admin\DataTables\Review\ReviewDataTable;
use App\Admin\Http\Requests\Review\ReviewRequest;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Admin\Traits\AuthService;

class ReviewController extends Controller
{
    use AuthService;
    public function __construct(
        ReviewRepositoryInterface $repository,
        ReviewServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }
    public function getView(): array
    {
        return [
            'index' => 'admin.reviews.index',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.review.index',
            'delete' => 'admin.review.delete',
        ];
    }

    public function index(ReviewDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách đánh giá'))
        ]);
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
