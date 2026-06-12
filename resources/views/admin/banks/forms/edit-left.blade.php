<div class="col-12 col-md-9">
    <div class="card">
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="bank_account" text="{{ __('Tên chủ thẻ') }}" icon="ti ti-pencil" required="true" />
                    <x-input :value="$bank->bank_account" name="bank_account" :required="true" :placeholder="__('Tên chủ thẻ')" />
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="bank_account_number" text="{{ __('Số tài khoản') }}" icon="ti ti-message"
                        required="true" />
                    <x-input :value="$bank->bank_account_number" name="bank_account_number" :required="true" :placeholder="__('Số tài khoản')" />
                </div>
            </div>

        </div>
    </div>
</div>
