@extends('admin.layouts.master')
@push('libs-css')
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('/public/libs/select2/dist/css/select2-bootstrap-5-theme.min.css') }}">
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form id="formVoucherProgram" :action="route('admin.voucher_program.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.voucher_programs.forms.create-left')
                    @include('admin.voucher_programs.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
    <script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
    <script src="{{ asset('/public/libs/select2/dist/js/select2.min.js') }}"></script>
@endpush

@push('custom-js')
    @include('admin.voucher_programs.scripts.scripts')
@endpush
