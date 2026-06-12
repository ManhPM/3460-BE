<script>
    function addProduct(payload, button) {
        // Disable button to prevent spam clicking
        button.addClass('disabled').prop('disabled', true);

        $.ajax({
            type: "GET",
            url: '{{ route('admin.flashsale.add_product') }}',
            data: payload,
            success: function(response) {
                // Ẩn row "chưa có sản phẩm" nếu có
                $('#tableProduct tbody tr:has(td[colspan])').remove();
                // Tạo jQuery element để áp dụng animation
                var $row = $(response.data);
                $row.hide();
                $('#tableProduct tbody').prepend($row);
                $row.slideDown(200).addClass('added-animate');
            },
            error: function(response) {
                if (response.status == 400) {
                    showToastify('error', 'Lỗi', 'Lỗi thêm vào danh sách flash sale');
                } else {
                    handleAjaxErrorNew(response);
                }
            },
            complete: function() {
                // Re-enable button after request completes
                button.removeClass('disabled').prop('disabled', false);
            }
        })
    }

    function closeModal(modal) {
        $(modal).find('.btn-close').trigger('click');
    }

    function checkAddProduct(productSlug, productVariationId) {
        var selector = '#tableProduct tbody tr.item-product' +
            '[data-product-slug="' + productSlug + '"]' +
            '[data-product-variation-id="' + (productVariationId || '') + '"]';
        return $(selector).length > 0;
    }

    function deleteItemFlashSaleDetail(id, elm) {
        $.ajax({
            type: "DELETE",
            url: '{{ route('admin.flashsale.deleteDetail') }}' + '/' + id,
            data: {
                _token: token
            },
            success: function(response) {
                removeElmItemFlashSaleDetail(elm);
                showToastify('success', 'Thành công', 'Xóa thành công!');
            },
            error: function(response) {
                handleAjaxErrorNew(response);
            }
        });
    }

    function removeElmItemFlashSaleDetail(elm) {
        var $row = $(elm).parents('.item-product');
        $row.addClass('removing-animate');
        setTimeout(function() {
            $row.slideUp(200, function() {
                $(this).remove();
                ensureNoItemRow();
            });
        }, 80);
    }

    function ensureNoItemRow() {
        var hasItems = $('#tableProduct tbody tr.item-product').length > 0;
        if (!hasItems) {
            var emptyRow = '<tr><td colspan="5" class="text-center py-4">\
                <div class="text-muted">\
                    <i class="ti ti-package-off fs-1 d-block mb-2"></i>\
                    <span>Chưa có sản phẩm nào trong chương trình flash sale</span>\
                </div></td></tr>';
            $('#tableProduct tbody').append(emptyRow);
        }
    }

    $(document).on('click', '.remove-item-product', function(e) {
        var id = $(this).data('id'),
            that = this;
        Swal.fire({
            icon: 'warning',
            title: 'Cảnh báo',
            text: 'Bạn có chắc chắn muốn xoá?',
            showCancelButton: true,
            confirmButtonText: 'Xác nhận',
            cancelButtonText: 'Hủy'
        }).then((result) => {
            if (result.isConfirmed) {
                if (id) {
                    deleteItemFlashSaleDetail(id, that);
                } else {
                    removeElmItemFlashSaleDetail(that)
                }
            } else if (result.dismiss === Swal.DismissReason.cancel) {}
        });
    })
    $(document).ready(function(e) {
        searchProduct('', '#showSearchResultProduct');
        $("#inputSearchProduct").keyup(debounce(500, function(e) {
            const key = document.querySelector('input[name="search_product"]');
            searchProduct(key.value, '#showSearchResultProduct');
        }));

        // Đồng bộ số lượng biến thể với input qty của dòng (nếu có)
        $(document).on('input', 'input[name="qty[]"]', function() {
            var $row = $(this).closest('tr');
            var $variationQty = $row.find('input[name="product_variation_qty[]"]');
            if ($variationQty.length) {
                $variationQty.val($(this).val());
            }
        });
    });

    function debounce(delay, callback) {
        let timer
        return function() {
            clearTimeout(timer)
            timer = setTimeout(() => {
                callback();
            }, delay)
        }
    }

    function searchProduct(keyword, elmRender) {
        $.ajax({
            type: "GET",
            url: '{{ route('admin.search.render_product_flash_sale') }}',
            data: {
                key: keyword
            },
            success: function(response) {
                $(elmRender).html(response);
            },
            error: function(response) {
                handleAjaxErrorNew(response);
            }
        })
    }


    function formatNumber(num) {
        return new Intl.NumberFormat('vi-VN').format(num);
    }

    // Validation trước khi submit form
    function validateFlashSaleForm() {
        var isValid = true;
        var errors = [];

        // Kiểm tra có sản phẩm nào không
        if ($('#tableProduct tbody tr.item-product').length === 0) {
            errors.push('Vui lòng thêm ít nhất một sản phẩm vào chương trình flash sale.');
            isValid = false;
        }

        // Kiểm tra số lượng và giá
        $('#tableProduct tbody tr.item-product').each(function(index) {
            var qty = $(this).find('input[name="qty[]"]').val();
            var flashsalePrice = $(this).find('input[name="flashsale_price[]"]').val();
            var variationPrice = $(this).find('input[name="product_variation_flashsale_price[]"]').val();

            if (!qty || qty <= 0) {
                errors.push(`Sản phẩm thứ ${index + 1}: Số lượng flash sale phải lớn hơn 0.`);
                isValid = false;
            }

            var price = flashsalePrice || variationPrice;
            if (!price || price < 0) {
                errors.push(`Sản phẩm thứ ${index + 1}: Giá flash sale phải lớn hơn hoặc bằng 0.`);
                isValid = false;
            }
        });

        if (!isValid) {
            showToastify('error', 'Lỗi', errors.join('<br>'));
        }

        return isValid;
    }

    // Xử lý submit form
    $(document).on('submit', 'form[data-flashsale-form]', function(e) {
        e.preventDefault();

        if (!validateFlashSaleForm()) {
            return false;
        }

        var form = $(this);
        var formData = new FormData(this);

        // Thêm CSRF token
        formData.append('_token', $('meta[name="csrf-token"]').attr('content'));

        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                showToastify('success', 'Thành công', 'Lưu chương trình flash sale thành công!');
                window.location.href = response.redirect || window.location.href;
            },
            error: function(response) {
                if (response.status === 422) {
                    var errors = response.responseJSON.errors;
                    var errorMessages = [];

                    for (var field in errors) {
                        errorMessages.push(errors[field].join('<br>'));
                    }

                    showToastify('error', 'Lỗi', errorMessages.join('<br>'));
                } else {
                    handleAjaxErrorNew(response);
                }
            }
        });
    });


    $(document).on('click', '.add-product', function(e) {
        var that = $(this),
            productSlug = that.data('product-slug');

        // Check if button is already disabled (request in progress)
        if (that.hasClass('disabled') || that.prop('disabled')) {
            return;
        }

        if (checkAddProduct(productSlug, '')) {
            showToastify('warning', 'Cảnh báo', 'Sản phẩm này đã có trong danh sách');
            return;
        }

        addProduct({
            product_slug: productSlug,
        }, that);
    })

    $(document).on('click', '.add-product-variation', function(e) {
        var that = $(this),
            productSlug = that.data('product-slug'),
            productVariationId = that.data('product-variation-id');

        // Check if button is already disabled (request in progress)
        if (that.hasClass('disabled') || that.prop('disabled')) {
            return;
        }

        if (checkAddProduct(productSlug, String(productVariationId))) {
            showToastify('warning', 'Cảnh báo', 'Biến thể sản phẩm này đã có trong danh sách');
            return;
        }
        addProduct({
            product_slug: productSlug,
            product_variation_id: productVariationId,
        }, that);
    })
</script>
