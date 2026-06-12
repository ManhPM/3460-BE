<?php
return [
    'images' => [
        'favicon' => '/public/assets/images/logo.png',
        'avatar' => '/public/assets/images/avatar-user.png',
        'default' => '/public/assets/images/no-image.png',
        'logo' => '/public/assets/images/logo.png',
        'norecord' => '/public/assets/images/norecord.svg',
        'default-rating' => '/public/assets/images/default-rating.png',
    ],
    'format' => [
        'datetime' => 'd-m-Y H:i:s',
        'date' => 'd-m-Y',
        'position_currency' => 'left'
    ],
    'currency' => env('CURRENCY_SYMBOL', '¥')
];
