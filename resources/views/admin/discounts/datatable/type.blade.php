<span @class([
    'badge',
    App\Enums\Discount\DiscountValueType::from($type)->badge(),
])>{{ \App\Enums\Discount\DiscountValueType::getDescription($type) }}</span>
