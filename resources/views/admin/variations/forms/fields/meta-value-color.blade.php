<div class="col-md-12 col-12">
    <div class="mb-3">
        <x-label text="{{ __('Màu sắc') }}" />
        <x-input type="color" class="form-control-color" name="meta_value[color]" :value="isset($variation)
            ? $variation->meta_value['color'] ?? '#ffffff'
            : old('meta_value[color]', '#ffffff')" :title="__('Chọn màu sắc')"
            :required="true" />
    </div>
</div>
