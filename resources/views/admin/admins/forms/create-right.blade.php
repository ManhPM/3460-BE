<div class="col-12 col-md-3">
    <div class="card">
        <div class="card-header">
            <span><i class="ti ti-playstation-circle me-2"></i>{{ __('Đăng') }}</span>
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
            <x-input-image-ckfinder name="avatar" showImage="avatar" :value="old('avatar')" />
        </div>
    </div>
</div>
