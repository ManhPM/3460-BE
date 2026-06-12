<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="title" text="{{ __('Tiêu đề') }}" icon="ti ti-article" required="true" />
                <x-input name="title" :value="old('title')" :required="true" placeholder="{{ __('Tiêu đề') }}" />
            </div>
        </div>

        <!-- desc -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="content" text="{{ __('Nội dung bài viết') }}" icon="ti ti-file-description" />
                <textarea name="content" class="ckeditor visually-hidden">
                    {{ old('content') }}
                </textarea>
            </div>
        </div>
        <!-- excerpt -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="excerpt" text="{{ __('Mô tả ngắn (Meta description)') }}"
                    icon="ti ti-file-description" />
                <x-textarea name="excerpt">{{ old('excerpt') }}</x-textarea>
            </div>
        </div>
    </div>
</div>
