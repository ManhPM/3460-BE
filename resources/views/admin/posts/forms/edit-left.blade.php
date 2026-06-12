<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="title" text="{{ __('Tiêu đề') }}" icon="ti ti-article" />
                <x-input name="title" :value="$post->title" :required="true" placeholder="{{ __('Tiêu đề') }}" />
            </div>
        </div>

        <!-- meta title -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="meta_title" text="{{ __('Tiêu đề (Meta title)') }}" icon="ti ti-article" />
                <x-input name="meta_title" :value="$post->meta_title" :required="true"
                    placeholder="{{ __('Tiêu đề (Meta title)') }}" />
            </div>
        </div>

        <!-- slug -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="slug" text="{{ __('Slug bài viết') }}" icon="ti ti-link" />
                <x-input name="slug" :value="$post->slug" :required="true" placeholder="{{ __('Slug bài viết') }}" />
            </div>
        </div>

        <!-- desc -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="content" text="{{ __('Nội dung bài viết') }}" icon="ti ti-file-description" />
                <textarea name="content" class="ckeditor visually-hidden">{{ $post->content }}</textarea>
            </div>
        </div>

        <!-- excerpt -->
        <div class="col-12">
            <div class="mb-3">
                <x-label for="excerpt" text="{{ __('Mô tả ngắn (Meta description)') }}"
                    icon="ti ti-file-description" />
                <x-textarea name="excerpt">{{ $post->excerpt }}</x-textarea>
            </div>
        </div>
    </div>
</div>
