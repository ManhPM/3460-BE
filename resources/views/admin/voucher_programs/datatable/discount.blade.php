@if ($type == App\Enums\Discount\DiscountValueType::Percent->value)
    {{ $discount_value }}%
@else
    {{ format_price($discount_value) }}
@endif
