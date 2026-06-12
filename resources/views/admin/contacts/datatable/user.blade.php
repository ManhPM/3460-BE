@if (isset($user))
    <x-link :href="route('admin.user.edit', $user['id'])" :title="$user['fullname']" />
@else
    {{ __('no_data') }}
@endif
