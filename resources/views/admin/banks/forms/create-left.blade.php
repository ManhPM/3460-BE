<div class="col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thêm thông tin chuyển khoản') }}</h2>
        </div>
        <div class="row card-body">
            <h3 class="bold-text">{{ __('Ngân hàng: ') . $bank->short_name . ' - ' . $bank->name }}</h3>
            <x-input :value="$bank->id" name="id" type="hidden" />
            <div class="mb-3">
                <x-label for="bank_account" text="{{ __('Tên chủ thẻ') }}" icon="ti ti-pencil" required="true" />
                <x-input :value="old('bank_account')" name="bank_account" :required="true" :placeholder="__('Tên chủ thẻ')" />
            </div>
            <div class="mb-3">
                <x-label for="bank_account_number" text="{{ __('Số tài khoản') }}" icon="ti ti-message"
                    required="true" />
                <x-input :value="old('bank_account_number')" name="bank_account_number" :required="true" :placeholder="__('Số tài khoản')" />
            </div>
        </div>
    </div>
</div>
