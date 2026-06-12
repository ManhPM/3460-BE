<div class="card hover-shadow border-0">
    <div class="position-relative">
        <img onclick="location.href='{{ route('user.product.detail', ['slug' => 1]) }}';" class="card-img-top img-default"
            src="https://img.global.news.samsung.com/vn/wp-content/uploads/2019/03/Galaxy-A50-Mat-truoc-3.jpg"
            style="cursor: pointer;" alt="Product 3">
        <img onclick="location.href='{{ route('user.product.detail', ['slug' => 1]) }}';" class="card-img-top img-hover"
            src="https://ttbh60s.com/wp-content/uploads/2020/03/Samsung-A50s.jpg" alt="Product 3"
            style="display: none;cursor: pointer;">
        <span class="badge badge-danger position-absolute end-0 top-0 m-3 text-white">50%</span>
        <span class="badge badge-featured position-absolute start-0 top-0 m-3">Nổi bật</span>
    </div>
    <div class="card-body shadow-sm">
        <h6 class="card-title mb-1"><a class="text-black" href="{{ route('user.product.detail', ['slug' => 1]) }}">Cell
                phone Silver</a></h6>
        <div class="rating fs-12">
            <span class="star text-warning">★</span>
            <span class="star text-warning">★</span>
            <span class="star text-warning">★</span>
            <span class="star text-warning">★</span>
            <span class="star text-warning">★</span>
            <span>100</span>
        </div>
        <p>
            <del>3,990,000₫</del>
            <strong class="text-red">2,990,000₫</strong>
        </p>
        <div class="product-hover text-center">
            <a style="cursor: pointer;" class="add-to-cart">
                <i class="fa fa-shopping-cart w-50" aria-hidden="true"></i><i class="fa fa-arrows-alt w-50"
                    data-product-id="1" onclick="openModal(this)" aria-hidden="true"></i>
            </a>
        </div>
    </div>
</div>
