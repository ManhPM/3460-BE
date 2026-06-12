@if (auth('web')->user())
    {{ $order['code'] }}
@else
    <x-link :href="route('admin.order.edit', $order_id)" :title="$order['code']" />
@endif
