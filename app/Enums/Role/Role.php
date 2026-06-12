<?php

namespace App\Enums\Role;


use App\Admin\Support\Enum;

enum Role: string
{
    use Enum;

    case Customer = "customer";

    case Driver = "driver";
    case SupperAdmin = "superAdmin";
    case Hotel = "hotel";

    case Store = "store";
    case Restaurant = "restaurant";

    case VehicleOwner = "vehicle_owner";

    public function badge(): string
    {
        return match ($this) {
            Role::Customer => 'bg-green-lt',
            Role::Driver => 'bg-red-lt',
            Role::SupperAdmin => 'bg-pink-lt',
            Role::Hotel => 'bg-blue-lt',
            Role::Store => 'bg-orange-lt',
            Role::Restaurant => 'bg-yellow-lt',
            Role::VehicleOwner => 'bg-yellow-lt'
        };
    }
}
