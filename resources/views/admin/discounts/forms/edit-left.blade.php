<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="card">
                <div class="card-header justify-content-between">
                    <h2 class="mb-0">{{ __('Sửa phiếu giảm giá') }}</h2>
                </div>
                <div class="card-body row">
                    <!-- name -->
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="code" text="{{ __('Mã phiếu giảm giá') }}"
                                icon="ti ti-rosette-discount me-1" required="true" />
                            <x-input name="code" :value="$discount->code" :required="true" :placeholder="__('code')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="max_usage" text="{{ __('Số lượng sử dụng tối đa') }}" icon="ti ti-ticket me-1"
                                required="true" />
                            <x-input name="max_usage" :value="$discount->max_usage" :required="true" :placeholder="__('Số lượng sử dụng tối đa')" />
                        </div>
                    </div>

                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="date_start" text="{{ __('Ngày bắt đầu') }}" icon="ti ti-calendar-event me-1"
                                required="true" />
                            <x-input class="flatpickr-dt" name="date_start" onblur="checkStartDate(this)"
                                :value="format_datetime($discount->date_start)" :required="true" :placeholder="__('Ngày bắt đầu')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="date_end" text="{{ __('Ngày kết thúc') }}" icon="ti ti-calendar-event me-1"
                                required="true" />
                            <x-input name="date_end" class="flatpickr-dt" onblur="checkEndDate(this)" :value="format_datetime($discount->date_end)"
                                :required="true" :placeholder="__('Ngày kết thúc')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="min_order_amount" text="{{ __('Giá trị đơn hàng tối thiểu để giảm giá') }}"
                                icon="ti ti-receipt-2 me-1" />
                            <x-input-price name="min_order_amount" id="min_order_amount" :value="$discount->min_order_amount"
                                :required="true" :placeholder="__('min_order_amount')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="discountValueType" text="{{ __('Loại giá trị giảm giá') }}"
                                icon="ti ti-cash me-1" />
                            <x-select id="discountValueType" name="type" :required="true">
                                @foreach ($types as $key => $value)
                                    <x-select-option :option="$discount->type->value" :value="$key" :title="$value" />
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                    <!-- price_selling -->
                    <div class="col-3">
                        <div class="mb-3">
                            <x-label for="discountValue" text="{{ __('Giá trị giảm giá') }}"
                                icon="ti ti-pentagram me-1" />
                            <x-input id="discountValue" onblur="checkDiscountValue(this)" name="discount_value"
                                :value="$discount->discount_value" :required="true" :placeholder="__('Giá trị giảm giá')" />
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="mb-3">
                            <x-label for="max_usage_per_user"
                                text="{{ __('Số lượng sử dụng tối đa cho 1 khách hàng') }}" icon="ti ti-user me-1" />
                            <x-input type="number" name="max_usage_per_user" :value="$discount->max_usage_per_user" :required="true"
                                :placeholder="__('Số lượng sử dụng tối đa cho 1 khách hàng')" />
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="mb-3">
                            <x-label for="max_discount_value" text="{{ __('Giá trị giảm giá tối đa') }}"
                                icon="ti ti-coin me-1" />
                            <x-input-price name="max_discount_value" id="max_discount_value" :value="$discount->max_discount_value"
                                :required="true" :placeholder="__('Giá trị giảm giá tối đa')" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const discountValueType = document.getElementById("discountValueType");
        const discountValue = document.getElementById("discountValue");

        // Hàm thay đổi hiển thị giá trị giảm giá
        function updateDiscountDisplay() {
            const selectedValue = discountValueType.value;
            const rawValue = discountValue.value.replace(/( VND| %)/g, ""); // Xóa bỏ định dạng hiển thị

            if (selectedValue == 2) {
                discountValue.value = rawValue ? rawValue + " VND" : "";
            } else if (selectedValue == 1) {
                discountValue.value = rawValue ? rawValue + " %" : "";
            }
        }

        // Lắng nghe sự kiện thay đổi loại giảm giá
        discountValueType.addEventListener("change", updateDiscountDisplay);

        const form = document.getElementById("formDiscount");
        // Xóa định dạng khi submit form
        form.addEventListener("submit", function(event) {
            discountValue.value = discountValue.value.replace(/(,| VND| %)/g, "");
        });

        // Cập nhật hiển thị lần đầu tiên
        updateDiscountDisplay();
    });
</script>
