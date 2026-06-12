@if (auth('web')->user())
    {{ '#' . $code }}
@else
    <x-link :href="route('admin.order.edit', $id)" :title="'#' . $code" />
@endif
