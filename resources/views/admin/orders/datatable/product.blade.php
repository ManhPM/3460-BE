@if (auth('web')->user())
    {{ $product_name }}
@else
    @if ($product_id)
        <x-link :href="route('admin.product.edit', $product_id)" :title="$product_name" />
    @else
        {{ $product_name }}
    @endif
@endif
