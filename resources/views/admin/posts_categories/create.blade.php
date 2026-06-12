@extends('admin.layouts.master')
@section('content')
				<div class="page-body">
								<div class="container-xl">
												<x-form :action="route('admin.post_category.store')" type="post" :validate="true">
																<div class="row justify-content-center">
																				@include('admin.posts_categories.forms.create-left')
																				@include('admin.posts_categories.forms.create-right')
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

@push('custom-js')
@endpush
