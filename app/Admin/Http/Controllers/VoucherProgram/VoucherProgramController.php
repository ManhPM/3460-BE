<?php

namespace App\Admin\Http\Controllers\VoucherProgram;

use App\Admin\DataTables\VoucherProgram\VoucherProgramDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\VoucherProgram\GiveVoucherRequest;
use App\Admin\Http\Requests\VoucherProgram\ResetVoucherProgramRequest;
use App\Admin\Http\Requests\VoucherProgram\VoucherProgramRequest;
use App\Admin\Repositories\VoucherProgram\VoucherProgramRepositoryInterface;
use App\Admin\Services\VoucherProgram\VoucherProgramServiceInterface;
use App\Enums\Discount\DiscountValueType;
use App\Enums\Notification\NotificationType;
use App\Enums\Voucher\VoucherType;
use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VoucherProgramController extends Controller
{
    protected $repository;

    public function __construct(
        VoucherProgramRepositoryInterface $repository,
        VoucherProgramServiceInterface    $service,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.voucher_programs.index',
            'create' => 'admin.voucher_programs.create',
            'edit' => 'admin.voucher_programs.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.voucher_program.index',
            'indexVoucher' => 'admin.voucher.index',
            'create' => 'admin.voucher_program.create',
            'edit' => 'admin.voucher_program.edit',
        ];
    }

    public function giveVoucher(GiveVoucherRequest $request): RedirectResponse
    {
        $response = $this->service->giveVoucher($request);
        if ($response) {
            return to_route($this->route['indexVoucher'])->with('success', __('success'));
        }

        return back()->with('error', __('fail'))->withInput();
    }

    public function reset(ResetVoucherProgramRequest $request): RedirectResponse
    {
        $response = $this->service->reset($request);
        if ($response) {
            return back()->with('success', __('success'));
        }

        return back()->with('error', __('fail'));
    }

    public function index(VoucherProgramDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách chương trình phát voucher'))
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách chương trình phát voucher'), route($this->route['index']))->add(__('add')),
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
            $this->crums->add(__('Danh sách chương trình phát voucher'), route($this->route['index']))->add(__('edit')),
            [
                'instance' => $instance,
                'types' => DiscountValueType::asSelectArray(),
                'voucherTypes' => VoucherType::asSelectArray(),
                'options' => NotificationType::asSelectArray(),
            ]
        );
    }


    public function store(VoucherProgramRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['edit']);
    }

    public function update(VoucherProgramRequest $request): RedirectResponse
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
