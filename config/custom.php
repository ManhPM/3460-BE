<?php
return [
    'images' => [
        'favicon' => '/public/assets/images/logo.png',
        'avatar' => '/public/assets/images/logo.png',
        'default' => '/public/assets/images/no-image.png',
        'logo' => '/public/assets/images/logo.png',
        'norecord' => '/public/assets/images/norecord.svg',
        'default-rating' => '/public/assets/images/default-rating.png',
    ],
    'format' => [
        'datetime' => 'd-m-Y H:i:s',
        'date' => 'd-m-Y',
        'position_currency' => env('POSITION_CURRENCY', 'right')
    ],
    'currency' => env('CURRENCY_SYMBOL', 'đ')
];
