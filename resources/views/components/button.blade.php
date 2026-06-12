<button type="{{ $type }}" {{ $attributes->merge(['class' => 'btn btn-default-cms']) }}>
    {{ $slot }}
</button>
