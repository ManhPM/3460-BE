<?php

namespace App\Admin\Traits;

use App\Models\Role;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

trait BaseAuthCMS
{

    /**
     * Get the ID of the currently authenticated user.
     *
     * @return int|null
     */
    public function getCurrentUserId(): ?int
    {
        return Auth::id();
    }

    /**
     * Check if the current user is authenticated.
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        return Auth::check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return Authenticatable|null
     */
    public function getCurrentUser(): ?Authenticatable
    {
        return Auth::user();
    }

    /**
     * Get the role of the currently authenticated user.
     *
     * @return string|null
     */
    public function getCurrentUserRole(): ?string
    {
        $user = $this->getCurrentUser();
        return $user?->roles;
    }

    /**
     * Check if the currently authenticated user is an admin.
     *
     * @return bool
     */
    public function isCurrentUserAdmin(): bool
    {
        return $this->getCurrentUserRole() === 'admin';
    }

    /**
     * Get the store ID of the currently authenticated store user.
     *
     * @return int|null
     */
    public function getCurrentStoreId(): ?int
    {
        return Auth::guard('store')->id();
    }

    /**
     * Get the currently authenticated store user.
     *
     * @return Authenticatable|null
     */
    public function getCurrentStoreUser(): ?Authenticatable
    {
        return Auth::guard('store')->user();
    }


    public function getCurrentAdmin(): ?Authenticatable
    {
        return Auth::guard('admin')->user();
    }

    public function getCurrentAdminId():?int{
        return Auth::guard('admin')->id();
    }

    public function getAllRolesByGuardName($guardName) {
        return Role::where('guard_name', $guardName)->get();
    }

    public function getAllRoles(): Collection
    {
        return Role::all();
    }


}
