<div class="col-12 col-md-3">
    <div class="card">
        <div class="card-header">
            <span><i class="ti ti-playstation-circle me-2"></i>{{ __('Đăng') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <x-button.submit :title="__('Cập nhật')" />
            @if ($admin->id !== 1)
                <x-button.modal-delete data-route="{{ route('admin.admin.delete', $admin->id) }}" :title="__('Xóa')" />
            @endif
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('avatar') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" :value="$admin->avatar" />
        </div>
    </div>
</div>
