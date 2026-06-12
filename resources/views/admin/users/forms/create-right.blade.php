<div class="col-12 col-md-3">
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
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('avatar') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="featureImage" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Đã xác thực email') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_email_verified" value="0">
            <x-input-switch name="is_email_verified" value="1" :label="__('Đã xác thực email?')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Đã xác thực số điện thoại') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_phone_verified" value="0">
            <x-input-switch name="is_phone_verified" value="1" :label="__('Đã xác thực số điện thoại?')" />
        </div>
    </div>
</div>
