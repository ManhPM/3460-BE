@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@push('custom-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.product.store')" enctype="multipart/form-data" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.products.forms.create-left')
                    @include('admin.products.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('public/libs/Parsley.js-2.9.2/comparison.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/i18n/vi.js') }}"></script>
    <script src="{{ asset('public/libs/jquery-ui/jquery-ui.js') }}"></script>
    @include('ckfinder::setup')
@endpush
@push('custom-js')
    @include('admin.products.scripts.scripts')
    @include('admin.products.scripts.script-attribute')
    @include('admin.products.scripts.script-variation')
@endpush
