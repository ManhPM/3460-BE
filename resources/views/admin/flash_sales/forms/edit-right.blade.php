<div class="col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body p-2 d-flex justify-content-between">
            <x-button.submit :title="__('Cập nhật')" />
            <x-button.modal-delete data-route="{{ route('admin.flashsale.delete', $instance->id) }}" :title="__('Xóa')" />
        </div>
    </div>

    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-toggle-right me-2"></i>{{ __('Trạng thái') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_active" value="0">
            <x-input-switch name="is_active" value="1" :label="__('Đang hoạt động?')" :checked="$instance->is_active == 1" />
        </div>
    </div>
</div>
