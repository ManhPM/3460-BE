<div id="price" class="tab-pane active show p-3" role="tabpanel" aria-labelledby="tabPrice">
    <div class="row mb-3">
        <label class="col-5 col-form-label"
            for="">{{ __('Giá bán thường') . ' (' . config('custom.currency') . ')' }} <span
                class="text-danger">*</span></label>
        <div class="col">
            <x-input-price id="product[price]" name="product[price]" :value="$product->price ?? old('product.price')" :placeholder="__('Giá bán thường')" />
        </div>
    </div>
    <div class="row mb-3">
        <label class="col-5 col-form-label"
            for="">{{ __('Giá khuyến mãi') . ' (' . config('custom.currency') . ')' }} <span
                class="text-danger">*</span></label>
        <div class="col">
            <x-input-price id="product[promotion_price]" name="product[promotion_price]" :value="$product->promotion_price ?? old('product.promotion_price')"
                :placeholder="__('Giá khuyến mãi')" />
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form'); // Chọn biểu mẫu của bạn

        form.addEventListener('submit', function(event) {
            const isContactPriceSwitch = document.querySelector('input[name="product[is_contact_price]"][type="checkbox"]');
            const isContactPrice = isContactPriceSwitch ? isContactPriceSwitch.checked : false;

            if (!isContactPrice) {
                const priceHidden = document.querySelector('#product\\[price\\]-hidden');
                const promotionPriceHidden = document.querySelector('#product\\[promotion_price\\]-hidden');
                const price = priceHidden ? (parseFloat(priceHidden.value) || 0) : 0;
                const promotionPrice = promotionPriceHidden ? (parseFloat(promotionPriceHidden.value) || 0) : 0;

                if (price > 0 && promotionPrice > 0) {
                    if (promotionPrice >= price) {
                        event.preventDefault();
                        showToastify('error', 'Lỗi', 'Giá khuyến mãi phải nhỏ hơn giá bán thường.');
                    }
                }
            }
        });
    });
</script>
