<?php

namespace App\Admin\Http\Controllers\Discount;

use App\Admin\DataTables\Discount\DiscountDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Discount\DiscountRequest;
use App\Admin\Repositories\Discount\DiscountRepositoryInterface;
use App\Admin\Services\Discount\DiscountServiceInterface;
use App\Enums\Discount\DiscountValueType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class DiscountController extends Controller
{
    protected $repository;

    public function __construct(
        DiscountRepositoryInterface $repository,
        DiscountServiceInterface    $service,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.discounts.index',
            'create' => 'admin.discounts.create',
            'edit' => 'admin.discounts.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.discount.index',
            'create' => 'admin.discount.create',
            'edit' => 'admin.discount.edit',
        ];
    }

    public function index(DiscountDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách mã giảm giá'))
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách mã giảm giá'), route($this->route['index']))->add(__('Thêm')),
            [
                'types' => DiscountValueType::asSelectArray()
            ]
        );
    }


    public function edit($id): Factory|View|Application
    {
        $discount = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách mã giảm giá'), route($this->route['index']))->add(__('Sửa')),
            [
                'discount' => $discount,
                'types' => DiscountValueType::asSelectArray()
            ]
        );
    }


    public function store(DiscountRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(DiscountRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
