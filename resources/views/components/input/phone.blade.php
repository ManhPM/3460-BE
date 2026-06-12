<input type="text" data-mask="0000000000" placeholder="0000000000" autocomplete="off"
    {{ $attributes->class(['form-control'])->merge([
            'placeholder' => __('Số điện thoại'),
        ])->merge($isRequired()) }}>
