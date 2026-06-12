<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Chuyên mục') }}</h2>
        </div>
        <div class="row card-body">
            <!-- name -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Tên Chuyên mục') }}" icon="ti ti-article" />
                    <x-input name="name" :value="$category->name" :required="true"
                        placeholder="{{ __('Tên Chuyên mục') }}" />
                </div>
            </div>

            <!-- slug -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="slug" text="{{ __('Slug Chuyên mục') }}" icon="ti ti-link" />
                    <x-input name="slug" :value="$category->slug" :required="true"
                        placeholder="{{ __('Slug Chuyên mục') }}" />
                </div>
            </div>

            <!-- desc -->
            <div class="col-md-12 col-sm-12">
                <div class="mb-3">
                    <x-label for="desc" text="{{ __('description') }}" icon="ti ti-file-description" />
                    <x-input name="desc" :value="$category->desc" :required="true" placeholder="{{ __('description') }}" />
                </div>
            </div>

        </div>
    </div>
</div>
