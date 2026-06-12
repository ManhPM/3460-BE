<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin Quyền') }}</h2>

        </div>
        <div class="row card-body">
            <!-- tile -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="title" text="{{ __('Tên Quyền') }}" />
                    <x-input name="title" :value="$permission->title" :required="true"
                        placeholder="{{ __('Ví dụ: Sửa bài viết') }}" />
                </div>
            </div>

            <!-- name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Slug') }}" />
                    <x-input name="name" :value="$permission->name" :required="true"
                        placeholder="{{ __('Viết liền không khoảng cách, không dấu dựa theo tên Quyền. Ví dụ: editPost') }}" />
                </div>
            </div>


            <!-- guard_name -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="guard_name" text="{{ __('Nhóm Quyền của') }}" />
                    <x-select name="guard_name" :required="true">
                        <x-select-option :option="$permission->guard_name" value="admin" title="Admin" />
                        <x-select-option :option="$permission->guard_name" value="web" title="Thành viên trên Web" />
                    </x-select>
                </div>
            </div>

            <!-- modules -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="module_id" text="{{ __('Thuộc Module') }}" />
                    <br />
                    <x-select name="module_id" :value="old('module_id')">
                        <x-select-option value="" title="{{ __('Không có') }}" />
                        @foreach ($listmodules as $module)
                            <x-select-option :option="$permission->module_id" :value="$module->id" :title="$module->name" />
                        @endforeach
                    </x-select>

                </div>
            </div>

        </div>
    </div>
</div>
