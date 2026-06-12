<div class="col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body p-2">
            <x-button.submit :title="__('Thêm')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Kích hoạt ngân hàng') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_active" value="0">
            <x-input-switch name="is_active" value="1" :label="__('Kích hoạt ngân hàng?')" />
        </div>
    </div>
</div>
