@if (isset($order))
    <x-link :href="route('admin.order.edit', $order['id'])" :title="'#' . $order['code']" />
@else
    {{ __('no_data') }}
@endif
