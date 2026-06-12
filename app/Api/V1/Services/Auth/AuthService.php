<?php

namespace App\Api\V1\Services\Auth;

use App\Admin\Repositories\CommissionWithdrawal\CommissionWithdrawalRepositoryInterface;
use App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Services\File\FileService;
use App\Admin\Traits\AuthService as TraitsAuthService;
use App\Admin\Traits\Roles;
use App\Api\V1\Services\Auth\AuthServiceInterface;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use App\Enums\User\Gender;
use App\Enums\WithdrawStatus;
use App\Mail\Authentication;
use App\Traits\UseLog;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AuthService implements AuthServiceInterface
{
    use Setup, Roles, UseLog, TraitsAuthService;

    protected $data;

    protected $repository;
    protected $cWRepository;
    private $fileService;

    protected $instance;

    public function __construct(
        UserRepositoryInterface $repository,
        CommissionWithdrawalRepositoryInterface $cWRepository,
        FileService $fileService
    ) {
        $this->repository = $repository;
        $this->cWRepository = $cWRepository;
        $this->fileService = $fileService;
    }

    public function store(Request $request)
    {

        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['gender'] = Gender::Male;
            $data['password'] = bcrypt($data['password']);
            $data['verify_code'] = random_int(1000, 9999);
            $data['verify_code_expiration'] = Carbon::now()->addMinutes(30);
            $data['membership_id'] = 1;
            if (env('IS_VERIFY_EMAIL')) {
                $data['is_email_verified'] = 0;
            } else {
                $data['is_email_verified'] = 1;
            }

            $user = $this->repository->findByField('email', $data['email']);

            if ($user) {
                $user->update($data);
                DB::commit();
                return $user;
            }

            $user = $this->repository->create($data);
            $roles = $this->getRoleCustomer();
            $this->repository->assignRoles($user, [$roles]);
            if (env('IS_VERIFY_EMAIL')) {
                Mail::to($user['email'])->send(new Authentication($user));
            }
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process create user', $e);
            return false;
        }
    }


    public function withdraw(Request $request)
    {
        DB::beginTransaction();

        try {
            $data = $request->validated();
            $user = $this->getCurrentUser();
            $data['user_id'] = $user->id;
            $data['status'] = WithdrawStatus::Pending->value;
            $instance = $this->cWRepository->create($data);
            DB::commit();
            return $instance;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process withdraw', $e);
            return false;
        }
    }

    public function update(Request $request)
    {

        $this->data = $request->validated();

        if (isset($this->data['password']) && $this->data['password']) {
            $this->data['password'] = bcrypt($this->data['password']);
        } else {
            unset($this->data['password']);
        }

        return $this->repository->update($this->data['id'], $this->data);
    }

    public function delete($id)
    {
        return $this->repository->delete($id);
    }

    public function updateTokenPassword(Request $request)
    {
        $user  = $this->repository->findByField('email', $request->input('email'));
        $this->data['token_get_password'] = $this->generateTokenGetPassword();
        $this->instance['user'] = $this->updateObject($user, $this->data);
        return $this;
    }

    public function generateRouteGetPassword($routeName)
    {
        $this->instance['url'] = URL::temporarySignedRoute(
            $routeName,
            now()->addMinutes(30),
            [
                'token' => $this->data['token_get_password'],
                'code' => $this->instance['user']->code
            ]
        );
        return $this;
    }

    public function generateRouteActivateAccount($routeName)
    {
        $this->instance['url'] = URL::temporarySignedRoute(
            $routeName,
            now()->addMinutes(30), // Thời hạn liên kết, có thể điều chỉnh
            [
                'token' => $this->data['verify_code'],
                'code' => $this->instance['user']->code,
            ]
        );
        return $this;
    }

    public function getInstance()
    {
        return $this->instance;
    }

    public function updateObject($user, $data)
    {
        $user->update($data);
        return $user;
    }
}
