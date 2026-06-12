@php
    $labels = [
        'deposit' => 'Nạp',
        'withdraw' => 'Rút',
        'payment' => 'Thanh toán',
        'refund' => 'Hoàn tiền',
    ];
@endphp
<span>{{ $labels[$type] ?? $type }}</span>


