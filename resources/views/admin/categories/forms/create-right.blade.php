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
            <i class="ti ti-photo-scan"></i>
            <span class="ms-2">{{ __('Avatar') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Hiện ở trang chủ mobile') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_home" value="0">
            <x-input-switch name="is_home" value="1" :label="__('Hiện ở trang chủ mobile?')" />
        </div>
    </div>
    @if (env('IS_MINIAPP'))
        <div class="card mb-3">
            <div class="card-header">
                <span><i class="ti ti-user-check me-2"></i>{{ __('Hiện ở trang chủ miniapp') }}</span>
            </div>
            <div class="card-body p-2">
                <input type="hidden" name="is_home_miniapp" value="0">
                <x-input-switch name="is_home_miniapp" value="1" :label="__('Hiện ở trang chủ miniapp?')" />
            </div>
        </div>
    @endif
</div>
