<?php

namespace App\Admin\Http\Controllers\CommissionWithdrawal;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Admin\Services\CommissionWithdrawal\CommissionWithdrawalServiceInterface;
use App\Admin\DataTables\CommissionWithdrawal\CommissionWithdrawalDataTable;
use App\Admin\Http\Requests\CommissionWithdrawal\CommissionWithdrawalRequest;
use App\Admin\Repositories\Order\OrderDetailRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Admin\Traits\AuthService;
use App\Enums\WithdrawStatus;

class CommissionWithdrawalController extends Controller
{
    use AuthService;

    protected $orderDetailRepository;

    public function __construct(
        CommissionWithdrawalRepositoryInterface $repository,
        OrderDetailRepositoryInterface $orderDetailRepository,
        CommissionWithdrawalServiceInterface $service,
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->orderDetailRepository = $orderDetailRepository;
        $this->service = $service;
    }
    public function getView(): array
    {
        return [
            'index' => 'admin.commission_withdrawals.index',
            'indexUser' => 'user.commission_withdrawals.index',
            'edit' => 'admin.commission_withdrawals.edit',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.commission_withdrawal.index',
            'indexUser' => 'user.commission_withdrawal.indexUser',
            'edit' => 'admin.commission_withdrawal.edit',
            'delete' => 'admin.commission_withdrawal.delete',
        ];
    }

    public function index(CommissionWithdrawalDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách đơn rút hoa hồng'))
        ]);
    }

    public function indexUser(CommissionWithdrawalDataTable $dataTable)
    {
        $auth = auth('web')->user();
        return $dataTable->render($this->view['indexUser'], [
            'auth' => $auth,
            'breadcrumbs' => $this->crums->add(__('Danh sách đơn rút hoa hồng'))->getBreadcrumbs()
        ]);
    }

    public function edit($id): View|Application
    {
        $response = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(
                __('Danh sách đơn rút tiền'),
                route($this->route['index'])
            )->add(__('edit')),
            [
                'commissionWithdrawal' => $response,
                'status' => WithdrawStatus::asSelectArray(),
            ]
        );
    }
    public function store(CommissionWithdrawalRequest $request): RedirectResponse
    {
        $commission_withdrawal = $this->service->store($request);
        if ($commission_withdrawal) {
            if (auth('admin')->user()) {
                return to_route($this->route['edit'], $commission_withdrawal->id);
            } else {
                return to_route($this->route['indexUser']);
            }
        }
        return back()->with('error', __('fail'));
    }
    public function update(CommissionWithdrawalRequest $request): RedirectResponse
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
