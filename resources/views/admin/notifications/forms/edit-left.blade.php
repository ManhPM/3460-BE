<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin thông báo') }}</h2>
        </div>
        <div class="row card-body">
            <!-- title -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="title" text="{{ __('Tiêu đề') }}" icon="ti ti-pencil" required="true" />
                    <x-input :value="$notification->title" name="title" :required="true" :placeholder="__('Tiêu đề')" />
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="short_message" text="{{ __('Mô tả ngắn') }}" icon="ti ti-message" required="true" />
                    <textarea name="short_message" class="form-control" rows="3" placeholder="{{ __('Mô tả ngắn') }}">{{ $notification->short_message }}</textarea>
                </div>
            </div>
            <!-- message -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="message" text="{{ __('Nội dung thông báo') }}" icon="ti ti-message"
                        required="true" />
                    <textarea name="message" class="ckeditor visually-hidden">{{ $notification->message }}</textarea>
                </div>
            </div>
        </div>
    </div>
</div>
