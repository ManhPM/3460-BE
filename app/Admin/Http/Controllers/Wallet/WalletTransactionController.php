<?php

namespace App\Admin\Http\Controllers\Wallet;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Repositories\WalletTransaction\WalletTransactionRepositoryInterface;
use App\Admin\Services\WalletTransaction\WalletTransactionServiceInterface;
use App\Admin\DataTables\WalletTransaction\WalletTransactionDataTable;

class WalletTransactionController extends Controller
{
    public function __construct(
        WalletTransactionRepositoryInterface $repository,
        WalletTransactionServiceInterface $service
    ) {
        parent::__construct();
        $this->repository = $repository;
        $this->service = $service;
    }

    public function getView(): array
    {
        return [
            'index' => 'admin.wallet_transaction.index',
            'show' => 'admin.wallet_transaction.show',
        ];
    }

    public function getRoute(): array
    {
        return [
            'index' => 'admin.wallet_transaction.index',
            'show' => 'admin.wallet_transaction.show',
        ];
    }

    public function index(WalletTransactionDataTable $dataTable)
    {
        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách giao dịch ví'))
        ]);
    }

    public function show($id)
    {
        $transaction = $this->repository->findOrFail($id, ['user']);

        return view($this->view['show'], [
            'transaction' => $transaction,
            'breadcrumbs' => $this->crums->add(__('Danh sách giao dịch ví'), route('admin.wallet_transaction.index'))
                ->add(__('Chi tiết giao dịch #' . $id))
        ]);
    }

    public function approve($id)
    {
        $result = $this->service->approve((int)$id);
        return back()->with($result ? 'success' : 'error', $result ? __('success') : __('fail'));
    }

    public function reject($id)
    {
        $result = $this->service->reject((int)$id);
        return back()->with($result ? 'success' : 'error', $result ? __('success') : __('fail'));
    }
}
