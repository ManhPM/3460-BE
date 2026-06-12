<span @class([
    'badge',
    'bg-green-lt' => $is_active,
    'text-bg-secondary' => !$is_active,
])>{{ $is_active ? __('Hoạt động') : __('Ngưng hoạt động') }}</span>
