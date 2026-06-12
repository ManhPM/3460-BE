@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.flashsale.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.flash_sales.forms.create-left')
                    @include('admin.flash_sales.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
    @include('admin.flash_sales.partials.modal-add-products')
@endsection

@push('libs-js')
    <script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.flash_sales.scripts.scripts')
@endpush
