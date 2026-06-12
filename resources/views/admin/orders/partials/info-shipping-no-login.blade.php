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
