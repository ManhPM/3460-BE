@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.attribute.variation.store')" type="post" :validate="true">
                <x-input type="hidden" name="attribute_id" :value="$attribute->id" />
                <div class="row justify-content-center">
                    @include('admin.variations.forms.create-left')
                    @include('admin.variations.forms.create-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
