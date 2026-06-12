<link rel="stylesheet" href="{{ asset('user/assets/css/cart/shopping-cart.css') }}">

<div class="cart-container" id="cartContainer">
    <button class="close-button">
        <i class="fas fa-times"></i>
    </button>

    <div class="cart-panel">
        <div data-price="150000" class="cart-item">
            <img src="{{ asset('/userfiles/images/phone/samsung-galaxy-s21-plus-256gb-bac-600x600-600x600.jpg') }}"
                alt="Product 1">
            <div class="cart-item-info">
                <h6>Cell phone X</h6>
                <p>150.000đ</p>
                <div class="quantity-controls">
                    <button class="minus-btn">-</button>
                    <span class="quantity">4</span>
                    <button class="plus-btn">+</button>
                </div>
                <p class="item-total">Tổng: 600.000đ</p>
            </div>
        </div>
    </div>

    <div class="cart-total">
        <p>35.000.000đ</p>
        <div>
            <button onclick="location.href='{{ route('user.cart.index') }}';" class="cart-button">Giỏ hàng</button>
            <button onclick="location.href='{{ route('user.cart.checkout') }}';" class="checkout-button">Thanh
                toán</button>
        </div>
    </div>
</div>

<script>
    const cartButton = document.querySelector('#cartButton');
    const cartContainer = document.getElementById('cartContainer');
    const closeShoppingCartButton = document.querySelector('.close-button');
    const checkoutButton = document.querySelector('.checkout-button');
    const minusBtns = document.querySelectorAll('.minus-btn');
    const plusBtns = document.querySelectorAll('.plus-btn');
    const quantitySpans = document.querySelectorAll('.quantity');
    const cartTotalElement = document.querySelector('.cart-total p');

    function removeCart(button, id) {
        var row = $(button).closest('.cart-item'); // Get the closest cart item element
        if (row) {
            Swal.fire({
                title: "Bạn có chắc chắn muốn thực hiện?",
                icon: "info",
                showCancelButton: true,
                confirmButtonColor: "var(--theme-color)",
                cancelButtonColor: "#d33",
                confirmButtonText: '<i class="ti ti-check"></i> Xác nhận',
                cancelButtonText: '<i class="ti ti-x"></i> Quay lại',
            }).then((result) => {
                if (result.isConfirmed) {
                    row.remove(); // Remove the cart item from UI

                    // Send DELETE request to server to remove the item from the cart
                    $.ajax({
                        type: "DELETE",
                        url: '{{ route('user.cart.remove') }}' + `/${id}`,
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            updateText(response); // Update cart information based on response
                        },
                        error: function(response) {
                            showToastify('error', 'Lỗi',
                                response.responseJSON.data ?
                                `${response.responseJSON.data.message}` :
                                `${response.responseJSON.message}`);
                            updateText(response.responseJSON); // Update UI on error
                        }
                    });

                    updateCartTotal(); // Update cart total after deletion
                }
            });
        }
    }


    function loadCartItems() {
        $.ajax({
            type: "GET",
            url: '{{ route('user.cart.items') }}',
            success: function(response) {
                console.log(response);
                let cartPanel = $('.cart-panel');
                cartPanel.empty();

                response.cart_items.forEach(item => {
                    const attributes = item.attributes.join(', ');

                    cartPanel.append(`
                    <div data-id="${item.id}" data-price="${item.price}" class="cart-item">
                        <img src="${item.image}" alt="${item.name}">
                        <div class="cart-item-info">
                            <h6>${item.name} <div class="product-color">${attributes}</div></h6>
                            <p>${formatPrice(item.price)}</p>
                            <div class="quantity-controls">
                                <button class="minus-btn" onclick="decrementCart(this, '${item.id}')">-</button>
                                <span class="quantity">${item.quantity}</span>
                                <button class="plus-btn" onclick="incrementCart(this, '${item.id}')">+</button>
                                <button class="remove-btn ms-3" onclick="removeCart(this, '${item.id}')">X</button>
                            </div>
                            <p class="item-total">Tổng: ${formatPrice(item.total_price)}</p>
                        </div>
                    </div>
                `);
                });

                attachQuantityHandlers(); // Attach event listeners to buttons
                $('.cart-total p').text(formatPrice(response.cart_total)); // Update total
            },
            error: function() {
                console.log('Failed to load cart items.');
            }
        });
    }



    function attachQuantityHandlers() {
        // Attach click listeners to minus buttons
        $('.minus-btn').on('click', function() {
            const cartItem = $(this).closest('.cart-item');
            const id = cartItem.data('id');
            decrementCart(this, id); // Pass the button and item ID to decrementCart
        });

        // Attach click listeners to plus buttons
        $('.plus-btn').on('click', function() {
            const cartItem = $(this).closest('.cart-item');
            const id = cartItem.data('id');
            incrementCart(this, id); // Pass the button and item ID to incrementCart
        });
    }

    function decrementCart(button, id) {
        if (requestDone) {
            requestDone = false;
            $(button).prop('disabled', true); // Disable button
            $.ajax({
                type: "POST",
                url: '{{ route('user.cart.decreament') }}',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const cartItem = $(button).closest('.cart-item');
                    const quantityElement = cartItem.find('.quantity');
                    let quantity = parseInt(quantityElement.text());

                    if (quantity > 1) {
                        quantity -= 1;
                        quantityElement.text(quantity);
                        updateItemTotal(cartItem, quantity);
                    } else {
                        cartItem.remove(); // Remove item from the cart if quantity reaches 0
                    }
                    updateCartTotal(); // Update total only after successful response
                    updateText(response);
                },
                error: function(response) {
                    showToastify('error', 'Lỗi',
                        `${response?.responseJSON?.message ?? 'Thao tác thất bại!'}`);
                },
                complete: function() {
                    requestDone = true;
                    $(button).prop('disabled', false); // Re-enable button
                }
            });
        }
    }

    function incrementCart(button, id) {
        if (requestDone) {
            requestDone = false;
            $(button).prop('disabled', true); // Disable button
            $.ajax({
                type: "POST",
                url: '{{ route('user.cart.increament') }}',
                data: {
                    id: id,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    const cartItem = $(button).closest('.cart-item');
                    const quantityElement = cartItem.find('.quantity');
                    let quantity = parseInt(quantityElement.text());

                    quantity += 1;
                    quantityElement.text(quantity);
                    updateItemTotal(cartItem, quantity);
                    updateCartTotal(); // Update total only after successful response
                    updateText(response);
                },
                error: function(response) {
                    showToastify('error', 'Lỗi',
                        `${response?.responseJSON?.message ?? 'Thao tác thất bại!'}`);
                },
                complete: function() {
                    requestDone = true;
                    $(button).prop('disabled', false); // Re-enable button
                }
            });
        }
    }

    function updateText(response) {
        $('#cart-count-mobile').text(response.data.count);
        $('#cart-count').text(response.data.count);
    }

    cartButton.addEventListener('click', () => {
        loadCartItems();
        cartContainer.classList.toggle('open');
    });

    closeShoppingCartButton.addEventListener('click', () => {
        cartContainer.classList.remove('open');
    });

    function updateItemTotal(cartItem, quantity) {
        const price = parseFloat(cartItem.data('price')); // Use jQuery to get data attribute
        const itemTotal = price * quantity;
        cartItem.find('.item-total').text(`Tổng: ` + formatPrice(itemTotal)); // Use jQuery to find and update element
        return itemTotal;
    }

    function updateCartTotal() {
        const cartItems = $('.cart-item'); // Use jQuery to select cart items
        let cartTotal = 0;

        cartItems.each(function() {
            const cartItem = $(this); // Wrap each item in jQuery
            const quantity = parseInt(cartItem.find('.quantity').text()); // Get quantity
            cartTotal += updateItemTotal(cartItem, quantity); // Update total
        });

        $('.cart-total p').text(formatPrice(cartTotal)); // Update cart total display
    }
</script>
