<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin biến thể') }} - <x-link class="text-primary" :href="route('admin.attribute.edit', optional($variation->attribute)->id)"
                    :title="optional($variation->attribute)->name" /></h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên biến thể') }}" icon="ti ti-tag" required="true" />
                    <x-input name="name" :value="$variation->name" :required="true"
                        placeholder="{{ __('Tên biến thể') }}" />
                </div>
            </div>
            @includeWhen($has_meta_value_color, 'admin.variations.forms.fields.meta-value-color')
            <!-- position -->
            <div class="col-md-12 col-12">
                <div class="mb-3">
                    <x-label for="position" text="{{ __('Vị trí') }}" icon="ti ti-location" required="true" />
                    <x-input type="number" name="position" :value="$variation->position" :required="true" />
                </div>
            </div>

            <!-- desc -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="desc" text="{{ __('Mô tả') }}" icon="ti ti-file-description" />
                    <x-textarea name="desc">{{ $variation->desc }}</x-textarea>
                </div>
            </div>
        </div>
    </div>
</div>
