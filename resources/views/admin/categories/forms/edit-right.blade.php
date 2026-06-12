<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <x-button.submit :title="__('Cập nhật')" />
            <x-button.modal-delete data-route="{{ route('admin.category.delete', $category->id) }}" :title="__('Xóa')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('Avatar') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" :value="$category->avatar" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Hiện ở trang chủ mobile') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_home" value="0">
            <x-input-switch name="is_home" value="1" :label="__('Hiện ở trang chủ mobile?')" :checked="$category->is_home == 1" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Hiện ở trang chủ miniapp') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_home_miniapp" value="0">
            <x-input-switch name="is_home_miniapp" value="1" :label="__('Hiện ở trang chủ miniapp?')" :checked="$category->is_home_miniapp == 1" />
        </div>
    </div>
</div>
