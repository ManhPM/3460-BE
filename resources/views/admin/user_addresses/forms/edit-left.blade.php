<div class="col-12 col-md-9">
    <div class="card mb-3">
        <div class="card-header">
            <h2 class="mb-0">{{ __('Thông tin địa chỉ') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-md-6">
                <div class="mb-3">
                    <x-label for="phone" text="{{ __('Số điện thoại') }}" icon="ti ti-phone" required="true" />
                    <x-input-phone name="phone" :value="$instance->phone" placeholder="{{ __('Số điện thoại') }}" />
                </div>
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <x-label for="email" text="{{ __('Email') }}" icon="ti ti-mail" />
                    <x-input name="email" type="email" :value="$instance->email ?? ''" placeholder="{{ __('Email') }}" />
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <x-label for="fullname" text="{{ __('Tên người nhận') }}" icon="ti ti-user" required="true" />
                <x-input name="fullname" :value="$instance->fullname" placeholder="{{ __('Tên người nhận') }}" />
            </div>
            <div class="col-md-6">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên địa điểm nhận') }}" icon="ti ti-user" required="true" />
                    <x-input name="name" :value="$instance->name" placeholder="{{ __('Tên địa điểm nhận') }}" />
                </div>
            </div>
            <div class="mb-3 col-md-6">
                <x-label for="address" text="{{ __('Địa chỉ') }}" icon="ti ti-map-pin" required="true" />
                <x-input name="address" :value="$instance->address" placeholder="{{ __('Địa chỉ') }}" />
            </div>
            <div class="col-md-6 mb-3">
                <x-label for="province_id" text="{{ __('Tỉnh/Thành phố:') }}" icon="ti ti-building" required="true" />
                <x-select name="province_id" id="province_id" class="select2-bs5-ajax"
                    data-url="{{ route('admin.search.select.province') }}" :required="true">
                    <x-select-option :option="$instance->province_id" :value="$instance->province_id" :title="$instance->province->name" />
                </x-select>
            </div>
            <div class="col-md-6 mb-3">
                <x-label for="ward_id" text="{{ __('Thành phố/Khu vực:') }}" icon="ti ti-building" required="true" />
                <x-select name="ward_id" id="ward_id" class="select2-bs5-ajax" data-url="" :required="true">
                    <x-select-option :option="$instance->ward_id" :value="$instance->ward_id" :title="$instance->ward->name" />
                </x-select>
            </div>
        </div>
    </div>
</div>
