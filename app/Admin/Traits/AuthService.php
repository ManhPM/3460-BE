<?php

namespace App\Admin\Traits;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

trait AuthService
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

    public function getCurrentAdmin(): ?Authenticatable
    {
        return Auth::guard('admin')->user();
    }

    public function getCurrentAdminId(): ?int
    {
        return Auth::guard('admin')->id();
    }
}
