<table id="tableTotalOrder" class="table table-sm">
    <thead class="d-none">
        <tr>
            <th class="text-center" style="width: 1%"></th>
            <th>{{ __('Sản phẩm') }}</th>
            <th class="text-center" style="width: 15%">{{ __('Số lượng') }}</th>
            <th class="text-end" style="width: 1%">{{ __('Đơn giá') }}</th>
            <th class="text-end" style="width: 1%">{{ __('Tổng tiền') }}</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Tạm tính') }}</td>
            <td class="text-end">{{ format_price($total ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Phí vận chuyển') }}</td>
            <td class="text-end text-danger">{{ format_price($shipping_fee ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Giảm giá') }}</td>
            <td class="text-end text-success">{{ format_price($discountValue ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Giảm giá vận chuyển bằng voucher') }}</td>
            <td class="text-end text-success">{{ format_price($voucher_shipping_discount_value ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Giảm giá tiền hàng bằng voucher') }}</td>
            <td class="text-end text-success">{{ format_price($voucher_product_discount_value ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Giảm giá bằng xu') }}</td>
            <td class="text-end text-success">{{ format_price($points_discount_value ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Giảm giá hạng thành viên') }}</td>
            <td class="text-end text-success">{{ format_price($membership_discount_value ?? 0) }}</td>
        </tr>
        <tr>
            <td colspan="4" class="text-end text-muted">{{ __('Giảm phí vận chuyển hạng thành viên') }}</td>
            <td class="text-end text-success">{{ format_price($membership_shipping_discount_value ?? 0) }}</td>
        </tr>
        <tr class="border-top">
            <td colspan="4" class="fw-bold text-uppercase text-end">{{ __('Tổng cộng') }}</td>
            <td class="text-end fw-bold">
                {{ format_price($final_total ?? 0) }}
            </td>
        </tr>
    </tbody>
    <x-input type="hidden" name="order[total]" :value="$total ?? 0" />
    <x-input type="hidden" name="order[points]" :value="$points ?? 0" />
    <x-input type="hidden" name="order[discount_value]" :value="$discountValue ?? 0" />
    <x-input type="hidden" name="order[shipping_fee]" :value="$shipping_fee ?? 0" />
    <x-input type="hidden" name="order[voucher_shipping_discount_value]" :value="$voucher_shipping_discount_value ?? 0" />
    <x-input type="hidden" name="order[voucher_product_discount_value]" :value="$voucher_product_discount_value ?? 0" />
</table>
