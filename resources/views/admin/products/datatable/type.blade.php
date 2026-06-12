@if($type == \App\Enums\Product\ProductType::Simple->value)
    <span class="badge bg-success">Đơn giản</span>
@else
    <span class="badge bg-warning">Biến thể</span>
@endif