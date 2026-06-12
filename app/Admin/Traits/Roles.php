<?php

namespace App\Admin\Traits;

use App\Enums\Role\Role;

trait Roles
{
    public function getRoleCustomer(): string
    {
        return Role::Customer->value;
    }

    public function getRoleSupperAdmin(): string
    {
        return Role::Driver->value;
    }

    public function getRoleDriver(): string
    {
        return Role::Driver->value;
    }

    public function getRoleHotel(): string
    {
        return Role::Hotel->value;
    }

    public function getRoleStore(): string
    {
        return Role::Store->value;
    }

    public function getRoleRestaurant(): string
    {
        return Role::Restaurant->value;
    }

    public function getRoleVehicleOwner(): string
    {
        return Role::VehicleOwner->value;
    }
}
