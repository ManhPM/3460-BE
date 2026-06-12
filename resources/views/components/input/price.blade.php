<input type="text"
    {{ $attributes->class(['form-control'])->merge([
            'data-parsley-number-message' => __('msgValidateNumber'),
            'data-format' => 'price',
        ])->merge($isRequired()) }}>
<input type="hidden" name="{{ $attributes->get('name') }}" id="{{ $attributes->get('name') }}-hidden">
<script src="{{ asset('public/libs/numeral/numeral.min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const currency = @json(config('custom.currency'));
        const currencyRegex = new RegExp(' ' + currency.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + '$');
        const priceInputs = document.querySelectorAll('input[data-format="price"]');
        priceInputs.forEach(function(priceInput) {
            const hiddenInputId = priceInput.id + '-hidden';
            const hiddenInput = document.getElementById(hiddenInputId);
            if (!hiddenInput) {
                console.error('Hidden input not found for ID:', hiddenInputId);
                return;
            }

            let initialValue = priceInput.value.replace(/\D/g, '');
            hiddenInput.value = initialValue;
            priceInput.value = numeral(initialValue).format('0,0') + ' ' + currency;

            priceInput.addEventListener('input', function() {
                const rawValue = this.value.replace(/\D/g, '');
                this.value = numeral(rawValue).format('0,0');
                hiddenInput.value = rawValue;
            });

            priceInput.addEventListener('focus', function() {
                this.value = this.value.replace(currencyRegex, '');
            });

            priceInput.addEventListener('blur', function() {
                const currentRawValue = this.value.replace(/\D/g, '');
                this.value = numeral(currentRawValue).format('0,0') + ' ' + currency;
                hiddenInput.value = currentRawValue;
            });
        });
    });
</script>
