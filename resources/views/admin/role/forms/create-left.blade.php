<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-center">
            <h2 class="mb-0">{{ __('Thông tin Vai trò') }}</h2>
        </div>
        <div class="row card-body">
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="title" text="{{ __('Tên vai trò') }}" icon="ti ti-user-edit" required="true" />
                    <x-input name="title" :value="old('title')" :required="true"
                        placeholder="{{ __('Ví dụ: Kế toán') }}" />
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="name" text="{{ __('Slug') }}" icon="ti ti-tag" required="true" />
                    <x-input name="name" :value="old('name')" :required="true"
                        placeholder="{{ __('Viết liền không khoảng cách, không dấu dựa theo tên vai trò. Ví dụ: ketoan') }}" />
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="guard_name" text="{{ __('Vai trò của ( Guard Name )') }}" icon="ti ti-user-check"
                        required="true" />
                    <x-select name="guard_name" :value="old('guard_name')" :required="true">
                        <x-select-option value="admin" title="Admin" />
                        <x-select-option value="web" title="Thành viên trên Web" />
                    </x-select>
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Phân quyền') }}" class="givePermissionsLabel" /><br />
                    <div id="checkAllPermissionsDiv"><input type="checkbox" id="checkAllPermissions"> Chọn tất cả</div>

                    <div class="row">
                        @foreach ($listPermissionsInAllModules as $moduleID => $permissionsListOfTheModule)
                            <div class="col-4">
                                <div class="mevivuModuleBox">
                                    <input type="checkbox" id="{{ $moduleID }}"
                                        class="checkboxPermission clickSelectAllPermissionInModule">
                                    <strong>{{ $listPermissionsInAllModules[$moduleID]['module_name'] }}</strong> <br />
                                    <br />
                                    @foreach ($listPermissionsInAllModules[$moduleID]['list'] as $permission)
                                        <input class="checkboxPermission checkboxFromModule_{{ $moduleID }}"
                                            name="permissions[]" value="{{ $permission->name }}" type="checkbox" />
                                        {{ $permission->title }} <br />
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        @foreach ($listpermissions as $permission)
                            <div class="col-4">
                                <input class="checkboxPermission" name="permissions[]" value="{{ $permission->name }}"
                                    type="checkbox" /> {{ $permission->title }}
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>
