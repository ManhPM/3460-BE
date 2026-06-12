@if (isset($products) && count($products) > 0)
    @each('admin.orders.partials.item-search-result-product', $products, 'product')
@else
    <div class="d-flex flex-column align-items-center justify-content-center text-center py-5">
        <i class="ti ti-package-off text-muted mb-3" style="font-size: 4rem;"></i>
        <p class="text-muted mb-0">{{ __('Không tìm thấy sản phẩm nào') }}</p>
        <small class="text-muted">{{ __('Vui lòng thử tìm kiếm với từ khóa khác') }}</small>
    </div>
@endif
