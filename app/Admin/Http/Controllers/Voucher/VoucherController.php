<?php

namespace App\Admin\Http\Controllers\Voucher;

use App\Admin\DataTables\Voucher\VoucherDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Voucher\VoucherRequest;
use App\Admin\Repositories\Voucher\VoucherRepositoryInterface;
use App\Admin\Services\Voucher\VoucherServiceInterface;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Voucher\VoucherType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    protected $repository;

    public function __construct(
        VoucherRepositoryInterface $repository,
        VoucherServiceInterface    $service,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.vouchers.index',
            'create' => 'admin.vouchers.create',
            'edit' => 'admin.vouchers.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.voucher.index',
            'create' => 'admin.voucher.create',
            'edit' => 'admin.voucher.edit',
        ];
    }

    public function index(VoucherDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách Voucher'))
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách Voucher'), route($this->route['index']))->add(__('add')),
            [
                'types' => DiscountValueType::asSelectArray(),
                'voucherTypes' => VoucherType::asSelectArray(),
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách Voucher'), route($this->route['index']))->add(__('edit')),
            [
                'instance' => $instance,
                'types' => DiscountValueType::asSelectArray(),
                'voucherTypes' => VoucherType::asSelectArray(),
            ]
        );
    }


    public function store(VoucherRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(VoucherRequest $request): RedirectResponse
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
