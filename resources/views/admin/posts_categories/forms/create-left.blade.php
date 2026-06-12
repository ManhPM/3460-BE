<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin chuyên mục') }}</h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên chuyên mục') }}" icon="ti ti-article" />
                    <x-input name="name" :value="old('name')" :required="true"
                        placeholder="{{ __('Tên chuyên mục') }}" />
                </div>
            </div>
            <!-- desc -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="desc" text="{{ __('description') }}" icon="ti ti-file-description" />
                    <x-input name="desc" :value="old('desc')" :required="true" placeholder="{{ __('description') }}" />
                </div>
            </div>
        </div>
    </div>
</div>
