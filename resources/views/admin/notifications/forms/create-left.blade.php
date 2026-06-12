<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin thông báo') }}</h2>
        </div>
        <div class="row card-body">
            <div class="mb-3">
                <x-label for="user_id[]" text="{{ __('Người dùng') }}" icon="ti ti-user" required="true" />
                <div class="fs-4 text-warning mb-2">
                    <i class="ti ti-info-circle me-1"></i>
                    {{ __('Nếu không chọn cụ thể đối tượng nào bên dưới, thông báo sẽ được gửi tới tất cả đối tượng thuộc loại đã chọn.') }}
                </div>
                <x-select name="user_id[]" class="select2-bs5-ajax" :data-url="route('admin.search.select.user')" id="user_id" multiple>
                </x-select>
            </div>
            <div class="mb-3">
                <x-label for="title" text="{{ __('Tiêu đề') }}" icon="ti ti-pencil" required="true" />
                <x-input name="title" :value="old('title')" :placeholder="__('Tiêu đề')" />
            </div>
            <div class="mb-3">
                <x-label for="short_message" text="{{ __('Mô tả ngắn') }}" icon="ti ti-message" required="true" />
                <textarea name="short_message" class="form-control">
                     {{ old('short_message') }}
                 </textarea>
            </div>
            <div class="mb-3">
                <x-label for="message" text="{{ __('Nội dung thông báo') }}" icon="ti ti-message" required="true" />
                <textarea name="message" class="ckeditor visually-hidden">
                     {{ old('message') }}
                    </textarea>
            </div>
        </div>
    </div>
</div>
