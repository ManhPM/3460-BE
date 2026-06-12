<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin đơn hàng') }}</h2>
        </div>
        <div class="row card-body">
            @if (!auth('admin')->user()->hasRole('branch'))
                <div class="mb-3">
                    <x-label for="order[admin_id]" text="{{ __('Chi nhánh') }}" icon="ti ti-building-store"
                        :required="true" />
                    <x-select name="order[admin_id]" id="admin_id" class="select2-bs5-ajax"
                        data-url="{{ route('admin.search.select.admin_branch') }}" :required="true">
                    </x-select>
                </div>
            @else
                <x-input type="hidden" name="order[admin_id]" :value="auth('admin')->user()->id" />
            @endif
            <div class="mb-3 col-md-6">
                <x-label for="user_id" text="{{ __('Khách hàng') }}" icon="ti ti-user" required="true" />
                <x-select name="order[user_id]" id="user_id" class="select2-bs5-ajax"
                    data-url="{{ route('admin.search.select.user') }}" :required="true">
                </x-select>
            </div>
            <div class="mb-3 col-md-6">
                <x-label text="{{ __('Tỉnh/Thành phố') }}" icon="ti ti-building" required="true" />
                <x-select name="order[province_id]" id="province_id" class="select2-bs5-ajax"
                    data-url="{{ route('admin.search.select.province') }}" :required="true">
                </x-select>
            </div>
            <div class="mb-3 col-md-6">
                <x-label text="{{ __('Thành phố/Khu vực') }}" icon="ti ti-building" required="true" />
                <x-select name="order[ward_id]" id="ward_id" class="select2-bs5-ajax" data-url=""
                    :required="true">
                </x-select>
            </div>
            <div class="mb-3">
                <x-label text="{{ __('Ghi chú') }}" icon="ti ti-note" />
                <x-textarea name="order[note]">{{ old('order.note') }}</x-textarea>
            </div>
            <div id="infoShipping" class="row">
                @include('admin.orders.partials.info-shipping')
            </div>
            @include('admin.orders.partials.products')
        </div>
    </div>
</div>
