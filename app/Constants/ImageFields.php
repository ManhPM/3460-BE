<?php

namespace App\Constants;

class ImageFields
{
    public static function getDriverFields(): array
    {
        return [
            'avatar',
            'id_card_front',
            'id_card_back',
            'license_plate_image',
            'vehicle_registration_front',
            'vehicle_registration_back',
            'driver_license_front',
            'driver_license_back',
            'vehicle_front_image',
            'vehicle_back_image',
            'vehicle_side_image',
            'vehicle_interior_image',
            'insurance_front_image',
            'insurance_back_image'
        ];
    }

}
