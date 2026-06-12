@props([
    'text' => '',
    'icon' => '',
    'required' => false,
    'class' => '',
    'for' => '',
])

<label for="{{ $for }}" class="form-label {{ $class }}" {{ $attributes }}>
    @if ($icon)
        <i class="{{ $icon }}"></i>
    @endif

    @if ($text)
        {{ $text }}
    @else
        {{ $slot }}
    @endif

    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
