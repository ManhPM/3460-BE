@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.role.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$role->id" />
                <div class="row justify-content-center">
                    @include('admin.role.forms.edit-left')
                    @include('admin.role.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- ckfinder js -->
@endpush

@push('custom-js')
    @include('admin.role.scripts.selectAllPermissions')
@endpush
