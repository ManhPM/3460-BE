<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin giá vận chuyển theo khu vực') }}</h2>
        </div>
        <div class="row card-body">
            <div class="mb-3">
                <x-label for="price" text="{{ __('Giá vận chuyển') }}" icon="ti ti-truck" required="true" />
                <x-input-price name="price" id="price" :value="old('price')" :required="true" :placeholder="__('Nhập giá vận chuyển')" />
            </div>
            <div class="mb-3">
                <x-label for="province_id" text="{{ __('Tỉnh/Thành phố') }}" icon="ti ti-building" required="true" />
                <x-select name="province_id" id="province_id" class="select2-bs5-ajax"
                    data-url="{{ route('admin.search.select.province') }}" :required="true">
                </x-select>
            </div>
            <div class="mb-3">
                <x-label for="ward_id" text="{{ __('Thành phố/Khu vực') }}" icon="ti ti-building" />
                <x-select name="ward_id" id="ward_id" class="select2-bs5-ajax" data-url="">
                </x-select>
            </div>
        </div>
    </div>
</div>
