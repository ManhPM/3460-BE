<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-playstation-circle me-2"></i>{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <div class="d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('Lưu')" name="submitter" value="save" />
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Kích hoạt ngân hàng') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_active" value="0">
            <x-input-switch name="is_active" value="1" :label="__('Kích hoạt ngân hàng?')" :checked="$bank->is_active == 1" />
        </div>
    </div>

</div>
