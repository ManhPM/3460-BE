<input type="color"
    {{ $attributes->class(['form-control-color'])->merge([
            'placeholder' => __('Chọn màu sắc'),
        ])->merge($isRequired()) }}>
