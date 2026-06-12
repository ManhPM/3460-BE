@extends('admin.layouts.master')
@push('libs-css')
@endpush
@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.slider.store')" type="post" :validate="true">
                <div class="row justify-content-center">
                    @include('admin.sliders.forms.create-left')
                    @include('admin.sliders.forms.create-right')
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
