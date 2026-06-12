<?php

namespace App\Http\Controllers\MembershipLevel;

use App\Http\Controllers\Controller;
use App\Admin\Traits\AuthService;
use App\Admin\Repositories\MembershipLevel\MembershipLevelRepositoryInterface;
use App\Api\V1\Support\Response;

class MembershipLevelController extends Controller
{
    use AuthService, Response;

    protected $repository;

    public function __construct(MembershipLevelRepositoryInterface $repository)
    {
        parent::__construct();
        $this->repository = $repository;
    }

    public function getView(): array
    {
        return [
            'index' => 'user.auth.membership',
        ];
    }

    public function index()
    {
        $membershipLevels = $this->repository->getAll();
        return view($this->view['index'], [
            'user' => $this->getCurrentUser(),
            'membershipLevels' => $membershipLevels,
            'breadcrumbs' => $this->crums->add(__('Hạng thành viên'))->getBreadcrumbs()
        ]);
    }
}
