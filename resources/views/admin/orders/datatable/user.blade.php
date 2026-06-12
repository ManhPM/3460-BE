@if (isset($user))
    @if (auth('web')->user())
        {{ $user['fullname'] }}
    @else
        <x-link :href="route('admin.user.edit', $user_id)" :title="$user['fullname']" />
    @endif
@else
    Khách hàng vãng lai
@endif
