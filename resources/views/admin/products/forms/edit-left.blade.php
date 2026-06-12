<div class="col-12 col-md-9">
    <div class="row">
        <!-- name -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="ti ti-brand-producthunt"></i>
                    <span class="ms-2">{{ __('Thông tin sản phẩm') }}</span>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <x-label for="product[name]" text="{{ __('Tên sản phẩm') }}" icon="ti ti-brand-producthunt"
                            required="true" />
                        <x-input name="product[name]" :value="$product->name" :required="true"
                            placeholder="{{ __('Tên sản phẩm') }}" />
                    </div>

                    <div class="mb-3">
                        <x-label for="product[desc]" text="{{ __('Mô tả') }}" icon="ti ti-file-description" />
                        <textarea name="product[desc]" class="ckeditor visually-hidden">{!! $product->desc !!}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <!-- data -->
        <div class="col-12">
            <div class="card mb-3">
                <div class="card-body p-0">
                    @include('admin.products.data.data-product')
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card mb-3">
                <div class="card-header">
                    <i class="ti ti-star"></i>
                    <span class="ms-2">{{ __('Đánh giá sản phẩm') }}</span>
                </div>
                <div class="card-body">
                    @php $totalReviews = array_sum($product->ratings->toArray()); @endphp
                    <h4 class="text-center mb-4">{{ 'Tổng quan đánh giá sản phẩm' }}</h4>
                    <div class="rating-summary">
                        @foreach ($product->ratings as $star => $count)
                            @php
                                $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                            @endphp
                            <div class="d-flex align-items-center mb-2">
                                <span class="me-2 text-end"
                                    style="width: 50px; font-weight: bold; display: inline-block;">{{ $star }}
                                    ⭐</span>
                                <div class="progress flex-grow-1" style="height: 15px;">
                                    <div class="progress-bar bg-warning" role="progressbar"
                                        style="width: {{ $percentage }}%;" aria-valuenow="{{ $percentage }}"
                                        aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                <span class="ms-2"
                                    style="min-width: 30px; text-align: right;">{{ $count }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
