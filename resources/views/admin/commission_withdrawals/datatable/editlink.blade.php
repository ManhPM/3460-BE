@if (auth('web')->user())
    {{ '#' . $id }}
@else
    <x-link :href="route('admin.commission_withdrawal.edit', $id)" :title="'#' . $id" />
@endif
