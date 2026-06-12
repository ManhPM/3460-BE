<button type="button" {{ $attributes->class(['submit-form', 'btn', 'btn-default-cms']) }}>

    @isset($icon)
        {{ $icon }}
    @endisset

    {{ $title ?? '' }}

    {{ $slot }}

</button>
