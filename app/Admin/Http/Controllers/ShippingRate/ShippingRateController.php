<?php

namespace App\Admin\Http\Controllers\ShippingRate;

use App\Admin\DataTables\ShippingRate\ShippingRateDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\ShippingRate\ShippingRateRequest;
use App\Admin\Repositories\ShippingRate\ShippingRateRepositoryInterface;
use App\Admin\Services\ShippingRate\ShippingRateServiceInterface;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ShippingRateController extends Controller
{
    protected $repository;

    public function __construct(
        ShippingRateRepositoryInterface $repository,
        ShippingRateServiceInterface    $service,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.shipping_rates.index',
            'create' => 'admin.shipping_rates.create',
            'edit' => 'admin.shipping_rates.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.shipping_rate.index',
            'create' => 'admin.shipping_rate.create',
            'edit' => 'admin.shipping_rate.edit',
        ];
    }

    public function index(ShippingRateDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách giá vận chuyển theo khu vực'))
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách giá vận chuyển theo khu vực'), route($this->route['index']))->add(__('add'))
        );
    }

    public function edit($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách giá vận chuyển theo khu vực'), route($this->route['index']))->add(__('edit')),
            [
                'instance' => $this->repository->findOrFail($id),
            ]
        );
    }

    public function store(ShippingRateRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(ShippingRateRequest $request): RedirectResponse
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
