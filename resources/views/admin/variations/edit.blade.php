@extends('admin.layouts.master')

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <x-form :action="route('admin.attribute.variation.update')" type="put" :validate="true">
                <x-input type="hidden" name="id" :value="$variation->id" />
                <div class="row justify-content-center">
                    @include('admin.variations.forms.edit-left')
                    @include('admin.variations.forms.edit-right')
                </div>
            </x-form>
        </div>
    </div>
@endsection
