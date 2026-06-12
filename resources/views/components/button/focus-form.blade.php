<button type="button" {{ $attributes->class(['focus-form', 'btn', 'btn-default-cms']) }}>

    @isset($icon)
        {{ $icon }}
    @endisset

    {{ $title ?? '' }}

    {{ $slot }}

</button>
