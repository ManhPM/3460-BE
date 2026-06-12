<?php

namespace App\Admin\Http\Controllers\Bank;

use App\Admin\DataTables\Bank\BankDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\Bank\BankRequest;
use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Admin\Services\Bank\BankServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BankController extends Controller
{
    public function __construct(
        BankRepositoryInterface $repository,
        BankServiceInterface    $service
    ) {

        parent::__construct();

        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.banks.index',
            'edit' => 'admin.banks.edit',
            'create' => 'admin.banks.create',
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.bank.index',
            'edit' => 'admin.bank.edit',
        ];
    }

    public function index(BankDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách ngân hàng'))
        ]);
    }

    public function create($id)
    {
        $bank = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách ngân hàng'), route($this->route['index']))->add(__('add')),
            [
                'bank' => $bank
            ]
        );
    }

    public function edit($id): View|Application
    {
        $response = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách ngân hàng'), route($this->route['index']))->add(__('edit')),
            [
                'bank' => $response
            ]
        );
    }


    public function store(BankRequest $request)
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(BankRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }
}
