<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Admin extends Authenticatable implements JWTSubject
{
    use HasRoles;
    use HasFactory;

    /**
     * The guard name for Spatie Permission
     *
     * @var string
     */
    protected $guard_name = 'admin';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'gender' => 'integer',
    ];


    /**
     * Get all possible model_type formats for this model
     * This handles the inconsistency between 'App\Models\Admin' and 'AppModelsAdmin'
     *
     * @return array
     */
    protected function getModelTypeVariants(): array
    {
        $className = get_class($this);
        // Standard format: 'App\Models\Admin'
        $standard = $className;
        // Old format without backslashes: 'AppModelsAdmin'
        $oldFormat = str_replace('\\', '', $className);

        return [$standard, $oldFormat];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'model_has_roles',  'model_id', 'role_id')
            ->wherePivotIn('model_type', $this->getModelTypeVariants())
            ->where('guard_name', $this->guard_name);
    }

    /**
     * Override permissions() method to ensure correct guard and model_type
     * This fixes the issue on DirectAdmin host where permissions are not loaded
     *
     * The issue on DirectAdmin is due to:
     * 1. model_type format mismatch: 'AppModelsAdmin' vs 'App\Models\Admin'
     * 2. Case sensitivity in model_type comparison
     * 3. Missing guard_name filter
     */
    public function permissions(): BelongsToMany
    {
        $permissionClass = config('permission.models.permission');

        return $this->belongsToMany(
            $permissionClass,
            config('permission.table_names.model_has_permissions'),
            config('permission.column_names.model_morph_key'),
            'permission_id'
        )
            ->wherePivotIn('model_type', $this->getModelTypeVariants())
            ->where('guard_name', $this->guard_name);
    }

    public function checkPermissions(array $permissionsArr): bool
    {
        if (empty($permissionsArr)) {
            return false;
        }

        // Try direct permissions first
        $hasDirectPermission = $this->permissions()
            ->whereIn('name', $permissionsArr)
            ->exists();

        if ($hasDirectPermission) {
            return true;
        }

        // Check permissions through roles
        return $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(function ($role) {
                return $role->permissions;
            })
            ->whereIn('name', $permissionsArr)
            ->where('guard_name', $this->guard_name)
            ->isNotEmpty();
    }


    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'guard' => 'admin',
            'type' => 'admin',
        ];
    }
}
