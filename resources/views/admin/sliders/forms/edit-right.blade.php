<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i>
            <span class="ms-2">{{ __('Đăng') }}</span>
        </div>
        <div class="card-body p-2 d-flex justify-content-between">
            <x-button.submit :title="__('Cập nhật')" />
            <x-button.modal-delete data-route="{{ route('admin.slider.delete', $slider->id) }}" :title="__('Xóa')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-toggle-right"></i>
            <span class="ms-2">{{ __('Trạng thái') }}</span>
        </div>
        <div class="card-body p-2">
            <x-select class="form-select" name="status" :required="true">
                @foreach ($status as $key => $value)
                    <x-select-option :option="$slider->status->value" :value="$key" :title="$value" />
                @endforeach
            </x-select>
        </div>
    </div>
</div>
