<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin thuộc tính') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên thuộc tính') }}" icon="ti ti-tag" required="true" />
                    <x-input name="name" :value="$instance->name" :required="true"
                        placeholder="{{ __('Tên thuộc tính') }}" />
                </div>
            </div>
            <!-- type -->
            <div class="col-12 col-md-6">
                <div class="mb-3">
                    <x-label for="type" text="{{ __('Loại') }}" icon="ti ti-category" />
                    <x-select class="select2-bs5" name="type" :required="true">
                        @foreach ($type as $key => $value)
                            <x-select-option :option="$attribute->type->value" :value="$key" :title="$value" />
                        @endforeach
                    </x-select>
                </div>
            </div>
            <!-- position -->
            <div class="col-md-6 col-12">
                <div class="mb-3">
                    <x-label for="position" text="{{ __('Vị trí') }}" icon="ti ti-location" />
                    <x-input type="number" name="position" :value="$attribute->position" :required="true" />
                </div>
            </div>
            <!-- desc -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="desc" text="{{ __('Mô tả') }}" icon="ti ti-file-description" />
                    <x-textarea name="desc">{{ $attribute->desc }}</x-textarea>
                </div>
            </div>
        </div>
    </div>
</div>
