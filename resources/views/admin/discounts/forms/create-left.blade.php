<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Tạo phiếu giảm giá') }}</h2>
                </div>
                <div class="card-body row">
                    <!-- name -->
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="code" text="{{ __('Mã phiếu giảm giá') }}"
                                icon="ti ti-rosette-discount me-1" required="true" />
                            <x-input name="code" :value="old('code')" :required="true" :placeholder="__('code')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="max_usage" text="{{ __('Số lượng sử dụng tối đa') }}" icon="ti ti-ticket me-1"
                                required="true" />
                            <x-input name="max_usage" :value="old('max_usage')" :required="true" :placeholder="__('Số lượng sử dụng tối đa')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="date_start" text="{{ __('Ngày bắt đầu') }}" icon="ti ti-calendar-event me-1"
                                required="true" />
                            <x-input class="flatpickr-dt" name="date_start" onblur="checkStartDate(this)"
                                :value="old('date_start')" :required="true" :placeholder="__('Ngày bắt đầu')" />
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
                            <x-label for="discountValueType" text="{{ __('Loại giá trị giảm giá') }}"
                                icon="ti ti-cash me-1" />
                            <x-select id="discountValueType" name="type" :required="true">
                                @foreach ($types as $key => $value)
                                    <x-select-option :value="$key" :title="$value" />
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <x-label for="discountValue" text="{{ __('Giá trị giảm giá') }}"
                                icon="ti ti-pentagram me-1" />
                            <x-input id="discountValue" onblur="checkDiscountValue(this)" name="discount_value"
                                :value="old('discount_value')" :required="true" :placeholder="__('Giá trị giảm giá')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="max_usage_per_user"
                                text="{{ __('Số lượng sử dụng tối đa cho 1 khách hàng') }}" icon="ti ti-user me-1" />
                            <x-input name="max_usage_per_user" :value="old('max_usage_per_user')" :required="true"
                                :placeholder="__('Số lượng sử dụng tối đa cho 1 khách hàng')" />
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
    const form = document.getElementById("formDiscount");
    // Xóa định dạng khi submit form
    form.addEventListener("submit", function(event) {
        discountValue.value = discountValue.value.replace(/(,| VND| %)/g, "");
    });
</script>
