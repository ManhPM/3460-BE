<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin slider') }}</h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên slider') }}" icon="ti ti-slideshow" required="true" />
                    <x-input name="name" :value="old('name')" :required="true" placeholder="{{ __('Tên slider') }}" />
                </div>
            </div>
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="plain_key" text="{{ __('Key') }}" icon="ti ti-key" required="true" />
                    <x-input name="plain_key" :value="old('plain_key')" :required="true"
                        placeholder="{{ __('Định danh slider') }}" />
                </div>
            </div>
            <!-- desc -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="desc" text="{{ __('Mô tả') }}" icon="ti ti-file-description" />
                    <x-textarea name="desc">{{ old('desc') }}</x-textarea>
                </div>
            </div>
        </div>
    </div>
</div>
