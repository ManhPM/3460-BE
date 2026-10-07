<?php

namespace App\Admin\Services\User;

use  App\Admin\Repositories\User\UserRepositoryInterface;
use App\Admin\Traits\Roles;
use Exception;
use Illuminate\Http\Request;
use App\Admin\Traits\Setup;
use App\Traits\UseLog;
use Illuminate\Support\Facades\DB;

class UserService implements UserServiceInterface
{
    use Setup, Roles, UseLog;

    protected array $data;

    protected UserRepositoryInterface $repository;

    public function __construct(UserRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(Request $request): object|false
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['password'] = bcrypt($data['password']);
            $data['membership_id'] = 1;
            $data['affiliate_code'] = !empty($data['phone']) ? $data['phone'] : $this->createAffiliateCode();


            $user = $this->repository->create($data);
            $roles = $this->getRoleCustomer();
            $this->repository->assignRoles($user, [$roles]);
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process create user', $e);
            return false;
        }
    }

    public function update(Request $request): object|bool
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            if (isset($data['password']) && $data['password']) {
                $data['password'] = bcrypt($data['password']);
            } else {
                unset($data['password']);
            }

            $currentUser = $this->repository->find($data['id']);
            if ($currentUser) {
                $newPhone = !empty($data['phone']) ? trim($data['phone']) : null;
                $oldAffiliateCode = $currentUser->affiliate_code;

                // Đồng bộ mã affiliate theo số điện thoại khi cập nhật trên CMS
                if ($newPhone && $oldAffiliateCode !== $newPhone) {
                    $data['affiliate_code'] = $newPhone;

                    // Đồng bộ cấp dưới (referrer_code) và hoa hồng đơn hàng cũ (order_details)
                    if ($oldAffiliateCode) {
                        \App\Models\User::where('referrer_code', $oldAffiliateCode)->update(['referrer_code' => $newPhone]);
                        \App\Models\OrderDetail::where('affiliate_code', $oldAffiliateCode)->update(['affiliate_code' => $newPhone]);
                    }
                }
            }

            $user = $this->repository->update($data['id'], $data);
            DB::commit();
            return $user;
        } catch (Exception $e) {
            DB::rollback();
            $this->logError('Failed to process update user', $e);
            return false;
        }
    }

    public function delete($id): object|bool
    {
        return $this->repository->delete($id);
    }
}
