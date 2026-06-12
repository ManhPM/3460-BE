<textarea data-bs-toggle="autosize" data-bs-toggle="autosize"
    {{ $attributes->class(['form-control'])->merge([
            'data-parsley-type-message' => __('Hãy nhập vào trường này.'),
        ])->merge($isRequired()) }}>{{ $slot }}</textarea>
