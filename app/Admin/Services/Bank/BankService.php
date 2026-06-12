<?php

namespace App\Admin\Services\Bank;

use App\Admin\Repositories\Bank\BankRepositoryInterface;
use App\Admin\Traits\AuthService;
use App\Traits\NotifiesViaFirebase;
use Illuminate\Http\Request;

class BankService implements BankServiceInterface
{
    use AuthService, NotifiesViaFirebase;

    protected $data;

    protected $repository;

    public function __construct(
        BankRepositoryInterface $repository,
    ) {
        $this->repository = $repository;
    }

    public function update(Request $request): object|bool
    {

        $this->data = $request->validated();

        return $this->repository->update($this->data['id'], $this->data);
    }

    public function store(Request $request)
    {
        $this->data = $request->validated();
        $bank = $this->repository->find($this->data['id']);
        $this->data = array_merge($bank->toArray(), $this->data);
        unset($this->data['id']);
        return $this->repository->create($this->data);
    }
}
