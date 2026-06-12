<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <x-button.submit :title="__('Cập nhật')" />
            <x-button.modal-delete data-route="{{ route('admin.order.delete', $order->id) }}" :title="__('Xóa')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-toggle-right"></i>
            <span class="ms-2">{{ __('Trạng thái') }}</span>
        </div>
        <div class="card-body p-2">
            <x-select class="form-select" name="order[status]" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$order->status->value" :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-credit-card me-2"></i>{{ __('Trạng thái thanh toán') }}</span>
        </div>
        <div class="card-body p-2">
            <x-select class="form-select" name="order[payment_status]" :required="true">
                @foreach ($payment_statuses as $key => $value)
                    <x-select-option :option="$order->payment_status->value" :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-gift"></i>
            <span class="ms-2">{{ __('Số điểm sử dụng') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input disabled :value="$order->points ?? 0" />
        </div>
    </div>
</div>
