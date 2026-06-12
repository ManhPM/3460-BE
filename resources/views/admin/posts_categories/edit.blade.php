@extends('admin.layouts.master')

@section('content')
				<div class="page-body">
								<div class="container-xl">
												<x-form :action="route('admin.post_category.update')" type="put" :validate="true">
																<x-input type="hidden" name="id" :value="$category->id" />
																<div class="row justify-content-center">
																				@include('admin.posts_categories.forms.edit-left')
																				@include('admin.posts_categories.forms.edit-right')
																</div>
												</x-form>
								</div>
				</div>
@endsection

@push('libs-js')
				<script src="{{ asset('public/libs/ckeditor/ckeditor.js') }}"></script>
				<script src="{{ asset('public/libs/ckeditor/adapters/jquery.js') }}"></script>
				<!-- ckfinder js -->
				@include('ckfinder::setup')
@endpush
