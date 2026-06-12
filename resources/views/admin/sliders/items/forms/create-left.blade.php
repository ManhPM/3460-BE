<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin slider item') }} - <x-link class="text-primary" :href="route('admin.slider.edit', $slider->id)"
                    :title="$slider->name" /></h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="title" text="{{ __('Tiêu đề') }}" icon="ti ti-text" required="true" />
                    <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('Tiêu đề') }}" />
                </div>
            </div>
            <!-- link -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="link" text="{{ __('Link') }}" icon="ti ti-link" required="true" />
                    <x-input name="link" :value="old('link', '#')" :required="true" placeholder="{{ __('link') }}" />
                </div>
            </div>
            <!-- position -->
            <div class="col-md-12 col-12">
                <div class="mb-3">
                    <x-label for="position" text="{{ __('Vị trí') }}" icon="ti ti-location" required="true" />
                    <x-input type="number" name="position" :value="old('position', 0)" :required="true" />
                </div>
            </div>

            <!-- mobile image -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <x-label for="mobile_avatar" text="{{ __('Hình ảnh mobile') }}" icon="ti ti-photo" />
                    <x-input-image-ckfinder name="mobile_avatar" showImage="mobile_avatar" :value="old('mobile_avatar')" />
                </div>
            </div>

            <!-- image -->
            <div class="col-12 col-md-12">
                <div class="mb-3">
                    <x-label for="image" text="{{ __('Hình ảnh') }}" icon="ti ti-photo" />
                    <x-input-image-ckfinder name="avatar" showImage="avatar" :value="old('avatar')" />
                </div>
            </div>
        </div>
    </div>
</div>
