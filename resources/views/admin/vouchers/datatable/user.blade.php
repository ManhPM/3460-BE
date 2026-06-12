@if (isset($user))
    <x-link :href="route('admin.user.edit', $user['id'])" :title="$user['fullname']" class="text-decoration-none" />
@endif
