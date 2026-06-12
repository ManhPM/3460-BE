<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="title" text="{{ __('Tiêu đề') }}" icon="ti ti-article" required="true" />
                <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('Tiêu đề') }}" />
            </div>
        </div>
        <div class="col-4">
            <div class="mb-3">
                <x-label for="position" text="{{ __('Vị trí') }}" icon="ti ti-cell" required="true" />
                <x-input type="number" name="position" :value="old('position')" :required="true"
                    placeholder="{{ __('Vị trí') }}" />
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-3">
                <div class="card-header">
                    <span><i class="ti ti-ti-toggle-right me-2"></i>{{ __('Ảnh nằm bên phải') }}</span>
                </div>
                <div class="card-body p-2">
                    <input type="hidden" name="is_rightside" value="0">
                    <x-input-switch name="is_rightside" value="1" :label="__('Ảnh nằm bên phải?')" />
                </div>
            </div>
        </div>
        <div class="col-4">
            <div class="card mb-3">
                <div class="card-header">
                    <span><i class="ti ti-ti-toggle-right me-2"></i>{{ __('Section có đang hoạt động') }}</span>
                </div>
                <div class="card-body p-2">
                    <input type="hidden" name="is_active" value="0">
                    <x-input-switch name="is_active" value="1" :label="__('Section có đang hoạt động?')" />
                </div>
            </div>
        </div>
    </div>
</div>
