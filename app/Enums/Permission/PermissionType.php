<?php

namespace App\Enums\Permission;


use App\Admin\Support\Enum;

enum PermissionType: string
{
    use Enum;

    case ADMIN = 'admin';
    case USER = 'user';


    public function badge(): string
    {
        return match ($this) {
            PermissionType::ADMIN => 'bg-red',
            PermissionType::USER => 'bg-blue',

        };
    }
}
