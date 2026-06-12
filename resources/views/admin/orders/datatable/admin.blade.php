@if (isset($admin))
    @if (auth('web')->user())
        {{ $admin['branch_name'] }}
    @else
        <x-link :href="route('admin.admin.edit', $admin_id)" :title="$admin['branch_name']" />
    @endif
@endif
