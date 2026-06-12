<div class="col-12 col-md-9">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Tạo voucher khách hàng') }}</h2>
                </div>
                <div class="card-body row">
                    <div class="mb-3">
                        <x-label for="user_id" text="{{ __('Khách hàng') }}" icon="ti ti-user" required="true" />
                        <x-select name="user_id" id="user_id" class="select2-bs5-ajax"
                            data-url="{{ route('admin.search.select.user') }}" :required="true">
                        </x-select>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="code" text="{{ __('Mã voucher') }}" icon="ti ti-rosette-discount me-1"
                                required="true" />
                            <x-input name="code" :value="old('code')" :required="true" :placeholder="__('code')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="date_end" text="{{ __('Ngày kết thúc') }}" icon="ti ti-calendar-event me-1"
                                required="true" />
                            <x-input name="date_end" class="flatpickr-dt" onblur="checkEndDate(this)" :value="old('date_end')"
                                :required="true" :placeholder="__('Ngày kết thúc')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="min_order_amount" text="{{ __('Giá trị đơn hàng tối thiểu để giảm giá') }}"
                                icon="ti ti-receipt-2 me-1" />
                            <x-input-price name="min_order_amount" id="min_order_amount" :value="old('min_order_amount')"
                                :required="true" :placeholder="__('Giá trị đơn hàng tối thiểu để giảm giá')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="voucher_type" text="{{ __('Loại voucher giảm giá') }}"
                                icon="ti ti-cash me-1" />
                            <x-select name="voucher_type" :required="true">
                                @foreach ($voucherTypes as $key => $value)
                                    <x-select-option :value="$key" :title="$value" />
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="type" text="{{ __('Loại giá trị giảm giá') }}" icon="ti ti-cash me-1" />
                            <x-select id="discountValueType" name="type" :required="true">
                                @foreach ($types as $key => $value)
                                    <x-select-option :value="$key" :title="$value" />
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <x-label for="discount_value" text="{{ __('Giá trị giảm giá') }}"
                                icon="ti ti-pentagram me-1" />
                            <x-input id="discountValue" onblur="checkDiscountValue(this)" name="discount_value"
                                :value="old('discount_value')" :required="true" :placeholder="__('Giá trị giảm giá')" />
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
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
</div>

<script>
    const form = document.getElementById("formVoucher");
    form.addEventListener("submit", function(event) {
        discountValue.value = discountValue.value.replace(/( VND| %|,)/g, "");
    });
</script>
