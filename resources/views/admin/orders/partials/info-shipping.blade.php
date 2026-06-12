@if (isset($order))
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Họ và tên') }}" icon="ti ti-user-edit" />
        <x-input name="order[fullname]" :value="$order->fullname" :placeholder="__('Họ và tên')" :required="true" />
    </div>
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Email') }}" icon="ti ti-mail" />
        <x-input-email name="order[email]" :value="$order->email" :required="true" />
    </div>
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Số điện thoại') }}" icon="ti ti-phone" />
        <x-input-phone name="order[phone]" :value="$order->phone" :required="true" />
    </div>
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Địa chỉ') }}" icon="ti ti-map-pin" />
        <x-input name="order[address]" :value="$order->address" :placeholder="__('Địa chỉ')" :required="true" />
    </div>
@else
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Họ và tên') }}" icon="ti ti-user-edit" required="true" />
        <x-input name="order[fullname]" :value="old('order.customer_fullname', $customer_fullname ?? '')" :placeholder="__('Họ và tên')" :required="true" />
    </div>
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Email') }}" icon="ti ti-mail" required="true" />
        <x-input-email name="order[email]" :value="old('order.customer_email', $customer_email ?? '')" :required="true" />
    </div>
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Số điện thoại') }}" icon="ti ti-phone" required="true" />
        <x-input-phone name="order[phone]" :value="old('order.customer_phone', $customer_phone ?? '')" :required="true" />
    </div>
    <div class="mb-3 col-md-6">
        <x-label text="{{ __('Địa chỉ') }}" icon="ti ti-map-pin" required="true" />
        <x-input name="order[address]" :value="old('order.shipping_address', $shipping_address ?? '')" :placeholder="__('Địa chỉ')" :required="true" />
    </div>
@endif
