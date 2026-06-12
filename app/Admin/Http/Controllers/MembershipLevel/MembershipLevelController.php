<?php

namespace App\Admin\Http\Controllers\MembershipLevel;

use App\Admin\DataTables\MembershipLevel\MembershipLevelDataTable;
use App\Admin\Http\Controllers\Controller;
use App\Admin\Http\Requests\MembershipLevel\MembershipLevelRequest;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\MembershipLevel\MembershipLevelServiceInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class MembershipLevelController extends Controller
{
    protected $userRepository;

    public function __construct(
        MembershipLevelRepositoryInterface $repository,
        UserRepositoryInterface $userRepository,
        MembershipLevelServiceInterface $service,
    ) {

        parent::__construct();
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->service = $service;
    }

    public function getView(): array
    {

        return [
            'index' => 'admin.membership_levels.index',
            'create' => 'admin.membership_levels.create',
            'edit' => 'admin.membership_levels.edit'
        ];
    }

    public function getRoute(): array
    {

        return [
            'index' => 'admin.membership_level.index',
            'create' => 'admin.membership_level.create',
            'edit' => 'admin.membership_level.edit',
        ];
    }

    public function setLevel()
    {
        try {
            DB::beginTransaction();
            $users = $this->userRepository->getAll();
            $levels = $this->repository->getAll(); // Danh sách các cấp độ thành viên

            foreach ($users as $user) {
                // Lọc ra level cao nhất mà user đủ điều kiện
                $newLevel = $levels->where('min_points', '<=', $user->membership_level_points)
                    ->sortByDesc('min_points')
                    ->first();

                if ($newLevel) {
                    $user->membership_id = $newLevel->id;
                }

                // Lưu thay đổi
                $user->save();
            }
            DB::commit();

            return back()->with('success', __('Hạng của các thành viên đã được cập nhật.'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return back()->with('error', __('Đã xảy ra lỗi, vui lòng thử lại sau.'));
        }
    }


    public function index(MembershipLevelDataTable $dataTable)
    {

        return $dataTable->render($this->view['index'], [
            'breadcrumbs' => $this->crums->add(__('Danh sách hạng thành viên'))
        ]);
    }


    public function create(): Factory|View|Application
    {
        return $this->renderView(
            $this->view['create'],
            $this->crums->add(__('Danh sách hạng thành viên'), route($this->route['index']))->add(__('Thêm'))
        );
    }

    public function edit($id): Factory|View|Application
    {
        $instance = $this->repository->findOrFail($id);

        return $this->renderView(
            $this->view['edit'],
            $this->crums->add(__('Danh sách hạng thành viên'), route($this->route['index']))->add(__('Sửa')),
            ['instance' => $instance]
        );
    }

    public function store(MembershipLevelRequest $request): RedirectResponse
    {
        return $this->handleStoreResponse($request, function ($request) {
            return $this->service->store($request);
        }, $this->route['index']);
    }

    public function update(MembershipLevelRequest $request): RedirectResponse
    {
        return $this->handleUpdateResponse($request, function ($request) {
            return $this->service->update($request);
        });
    }

    public function delete($id): RedirectResponse
    {
        if ($id == 1) {
            return back()->with('error', __('Không thể xóa hạng thành viên khởi đầu.'));
        }
        $instance = $this->repository->findOrFail($id);
        if (isset($instance->users[0])) {
            return back()->with('error', __('Không thể xóa hạng thành viên này.'));
        }
        return $this->handleDeleteResponse($id, function ($id) {
            return $this->service->delete($id);
        }, $this->route['index']);
    }
}
