<?php

namespace App\Admin\Http\Controllers\UserAddress;

use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\UserAddress\UserAddressRequest;
use App\Admin\Repositories\UserAddress\UserAddressRepositoryInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class UserAddressController extends Controller
{
    protected $repository;
    public function __construct(
        UserAddressRepositoryInterface $repository,
    ) {

        parent::__construct();
        $this->repository = $repository;
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.user_addresses.index',
            'create' => 'admin.user_addresses.create',
            'edit' => 'admin.user_addresses.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.user.index',
            'create' => 'admin.user_address.create',
            'edit' => 'admin.user.edit',
        ];
    }


    public function create($id): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách khách hàng'), route($this->route['index']))->add(__('Thêm địa chỉ')),
            [
                'userId' => $id
            ]
        );
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách khách hàng'), route($this->route['index']))->add(__('Sửa địa chỉ')),
            [
                'instance' => $instance
            ]
        );
    }


    public function store(UserAddressRequest $request): RedirectResponse
    {
        $result = $this->repository->create($request->validated());
        if ($result) {
            return to_route($this->route['edit'], ['id' => $result->user_id])->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }

    public function update(UserAddressRequest $request): RedirectResponse
    {
        $result = $this->repository->update($request->id, $request->validated());
        if ($result) {
            return to_route($this->route['edit'], ['id' => $result->user_id])->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }

    public function delete($id): RedirectResponse
    {
        $instance = $this->repository->findOrFail($id);
        $result = $this->repository->delete($id);

        if ($result) {
            return to_route($this->route['edit'], ['id' => $instance->user_id])->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }

    public function setDefault($id)
    {
        $result = $this->repository->update($id, ['is_default' => 1]);
        if ($result) {
            $addresses = $this->repository->getBy(['user_id' => $result->user_id]);
            foreach ($addresses as $address) {
                if ($address->id != $id) {
                    $this->repository->update($address->id, ['is_default' => 0]);
                }
            }
            return back()->with('success', __('success'));
        }
        return back()->with('error', __('fail'));
    }
}
