<div class="col-12 col-md-9">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Tạo chương trình phát voucher cho khách hàng') }}</h2>
                </div>
                <div class="card-body row">
                    <div class="col-md-9 mb-3">
                        <x-label for="name" text="{{ __('Tên của chương trình') }}" icon="ti ti-gift-card me-1"
                            required="true" />
                        <x-input name="name" :value="old('name')" :required="true" :placeholder="__('Tên của chương trình')" />
                    </div>
                    <div class="col-md-3 mb-3">
                        <x-label for="qty" text="{{ __('Số lượng phiếu') }}" icon="ti ti-gift-card me-1"
                            required="true" />
                        <x-input type="number" name="qty" :value="old('qty')" :required="true" :placeholder="__('Số lượng phiếu')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="expiration_days" text="{{ __('Số ngày hết hạn sau khi phát') }}"
                            icon="ti ti-calendar-event me-1" required="true" />
                        <x-input type="number" name="expiration_days" :value="old('expiration_days')" :required="true"
                            :placeholder="__('Nhập số ngày')" min="1" />
                        <small
                            class="text-muted">{{ __('Voucher sẽ hết hạn sau số ngày này kể từ khi được phát cho khách hàng') }}</small>
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="min_order_amount" text="{{ __('Giá trị đơn hàng tối thiểu để giảm giá') }}"
                            icon="ti ti-receipt-2 me-1" />
                        <x-input-price name="min_order_amount" id="min_order_amount" :value="old('min_order_amount')"
                            :required="true" :placeholder="__('Giá trị đơn hàng tối thiểu để giảm giá')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="voucher_type" text="{{ __('Loại voucher giảm giá') }}" icon="ti ti-cash me-1" />
                        <x-select name="voucher_type" :required="true">
                            @foreach ($voucherTypes as $key => $value)
                                <x-select-option :value="$key" :title="$value" />
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="type" text="{{ __('Loại giá trị giảm giá') }}" icon="ti ti-cash me-1" />
                        <x-select id="discountValueType" name="type" :required="true">
                            @foreach ($types as $key => $value)
                                <x-select-option :value="$key" :title="$value" />
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="discount_value" text="{{ __('Giá trị giảm giá') }}" icon="ti ti-pentagram me-1" />
                        <x-input id="discountValue" onblur="checkDiscountValue(this)" name="discount_value"
                            :value="old('discount_value')" :required="true" :placeholder="__('Giá trị giảm giá')" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <x-label for="max_discount_value" text="{{ __('Giá trị giảm giá tối đa') }}"
                            icon="ti ti-coin me-1" />
                        <x-input-price name="max_discount_value" id="max_discount_value" :value="old('max_discount_value')"
                            :required="true" :placeholder="__('Giá trị giảm giá tối đa')" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.getElementById("formVoucherProgram");
        const discountValue = document.getElementById("discountValue");
        form.addEventListener("submit", function(event) {
            discountValue.value = discountValue.value.replace(/( VND| %|,)/g, "");
        });
    });
</script>
