<div class="col-12 col-md-9">
    <div class="card">
        <div class="card-header justify-content-between">
            <h2 class="mb-0">{{ __('Thông tin vai trò') }}</h2>

        </div>
        <div class="row card-body">
            <!-- tile -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="title" text="{{ __('Tên vai trò') }}" icon="ti ti-user-edit" required="true" />
                    <x-input name="title" :value="$role->title" :required="true"
                        placeholder="{{ __('Ví dụ: Kế toán') }}" />
                </div>
            </div>
            <div class="col-12">
                <div class="mb-3">
                    <x-label for="guard_name" text="{{ __('Vai trò của ( Guard Name )') }}" icon="ti ti-user-check"
                        required="true" />
                    <x-select name="guard_name" :required="true">
                        <x-select-option :option="$role->guard_name" value="admin" title="Admin" />
                        <x-select-option :option="$role->guard_name" value="web" title="Thành viên trên Web" />
                    </x-select>
                </div>
            </div>

            <hr />
            <!-- permissions -->
            <div class="col-12">
                <div class="mb-3">
                    <x-label text="{{ __('Phân quyền') }}" class="givePermissionsLabel" /><br />
                    <div class="mb-3">
                        <x-label text="{{ __('Tìm kiếm quyền hoặc module') }}" />
                        <div class="input-icon">
                            <x-input id="searchPermission" type="text" value="" class="form-control"
                                placeholder="Nhập từ khóa..." />
                            <span class="input-icon-addon">
                                <i class="ti ti-search"></i>
                            </span>
                        </div>
                    </div>
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
                                            type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                            {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                        {{ $permission->title }}<br>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-4">
                                <input class="checkboxPermission" type="checkbox" name="permissions[]"
                                    value="{{ $permission->name }}"
                                    {{ $role->permissions->contains($permission->id) ? 'checked' : '' }}>
                                {{ $permission->title }}<br>
                            </div>
                        @endforeach
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        let searchInput = document.getElementById("searchPermission");

        searchInput.addEventListener("keyup", function() {
            let keyword = this.value.toLowerCase();
            let moduleBoxes = document.querySelectorAll(".mevivuModuleBox");

            moduleBoxes.forEach(function(box) {
                let text = box.innerText.toLowerCase();
                if (text.includes(keyword)) {
                    box.style.display = "block"; // Hiển thị nếu khớp từ khóa
                } else {
                    box.style.display = "none"; // Ẩn nếu không khớp
                }
            });
        });
    });
</script>
