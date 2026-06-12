<div class="col-12 col-md-3">
    <div class="card mb-3">
        <div class="card-header">
            <i class="ti ti-playstation-circle"></i><span class="ms-2">{{ __('action') }}</span>
        </div>
        <div class="card-body d-flex justify-content-between p-2">
            <div class="d-flex align-items-center h-100 gap-2">
                <x-button.submit :title="__('save')" name="submitter" value="save" />
            </div>
            <x-button.modal-delete data-route="{{ route('admin.voucher_program.delete', $instance->id) }}"
                :title="__('delete')" />
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
