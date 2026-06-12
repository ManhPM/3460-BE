<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i><span class="ms-2">{{ __('action') }}</span>
        </div>

        <div class="card-body p-2">
            <div class="d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save" />
            </div>
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <span><i class="ti ti-user-check me-2"></i>{{ __('Voucher đã được sử dụng') }}</span>
        </div>
        <div class="card-body p-2">
            <input type="hidden" name="is_used" value="0">
            <x-input-switch name="is_used" value="1" :label="__('Voucher đã được sử dụng?')" />
        </div>
    </div>
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-photo"></i>
            <span class="ms-2">{{ __('avatar') }}</span>
        </div>
        <div class="card-body p-2">
            <x-input-image-ckfinder name="avatar" showImage="avatar" class="img-fluid" />
        </div>
    </div>
</div>
