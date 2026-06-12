<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <x-button.submit :title="__('Cập nhật')" />
            <x-button.modal-delete data-route="{{ route('admin.user.delete', $instance->id) }}" :title="__('Xóa')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Đã xác thực email') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_email_verified" value="0">
            <x-input-switch name="is_email_verified" value="1" :label="__('Đã xác thực email?')" :checked="$instance->is_email_verified == 1" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Đã xác thực số điện thoại') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_phone_verified" value="0">
            <x-input-switch name="is_phone_verified" value="1" :label="__('Đã xác thực số điện thoại?')" :checked="$instance->is_phone_verified == 1" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('avatar') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" class="img-fluid" :value="$instance->avatar" />
        </div>
    </div>
</div>
