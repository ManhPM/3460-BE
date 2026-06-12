@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.slider.item.store')" type="post" :validate="true">
                <x-input type="hidden" name="slider_id" :value="$slider->id" />
                <div class="row justify-content-center">
                    @include('admin.sliders.items.forms.create-left')
                    @include('admin.sliders.items.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection

@push('libs-js')
    @include('ckfinder::setup')
@endpush
