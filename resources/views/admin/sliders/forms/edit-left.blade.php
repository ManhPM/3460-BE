<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h3 class="mb-0">{{ __('Thông tin slider') }}</h3>
            <x-link :href="route('admin.slider.item.index', $slider->id)" :title="__('Các Slider items')" />
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên slider') }}" icon="ti ti-slideshow" required="true" />
                    <x-input name="name" :value="$slider->name" :required="true" placeholder="{{ __('Tên slider') }}" />
                </div>
            </div>
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="plain_key" text="{{ __('Key') }}" icon="ti ti-key" required="true" />
                    <x-input name="plain_key" :value="$slider->plain_key" :required="true"
                        placeholder="{{ __('Định danh slider') }}" />
                </div>
            </div>
            <!-- desc -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="desc" text="{{ __('Mô tả') }}" icon="ti ti-file-description" required="true" />
                    <x-textarea name="desc">{{ $slider->desc }}</x-textarea>
                </div>
            </div>
        </div>
    </div>
</div>
