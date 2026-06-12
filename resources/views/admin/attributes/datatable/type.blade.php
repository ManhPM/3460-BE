<span @class([
    'badge',
    App\Enums\Attribute\AttributeType::from($type)->badge(),
])>{{ \App\Enums\Attribute\AttributeType::getDescription($type) }}</span>
