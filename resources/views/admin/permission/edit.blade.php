@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.permission.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$permission->id" />
                <div class="row justify-content-center">
                    @include('admin.permission.forms.edit-left')
                    @include('admin.permission.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    <!-- ckfinder js -->
@endpush

@push('custom-js')
@endpush
