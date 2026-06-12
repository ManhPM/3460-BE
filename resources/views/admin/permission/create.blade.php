@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.permission.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.permission.forms.create-left')
                    @include('admin.permission.forms.create-right')
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
