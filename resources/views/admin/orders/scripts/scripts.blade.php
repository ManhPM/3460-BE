<script>
    let provinceId;

    function searchProduct(keyword, elmRender) {
        $(elmRender).addClass('position-relative').html(
            '<div class="text-center py-4 text-muted"><span class="spinner-border spinner-border-sm me-2"></span>{{ __('Đang tìm kiếm...') }}</div>'
        );
        $.ajax({
            type: "GET",
            url: '{{ route('admin.search.render_product_and_variation') }}',
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

    function addProduct(payload, button) {
        // Disable button to prevent spam clicking
        button.addClass('disabled').prop('disabled', true);

        $.ajax({
            type: "GET",
            url: '{{ route('admin.order.add_product') }}',
            data: payload,
            success: function(response) {
                $('#tableProduct tbody').prepend(response.data);
                $('#tableProduct tbody tr.no-product').hide();
                // Highlight newly added row
                var $first = $('#tableProduct tbody tr.item-product').first();
                $first.addClass('table-success');
                setTimeout(function() {
                    $first.removeClass('table-success');
                }, 1200);
                reloadTotalOrder();
            },
            error: function(response) {
                handleAjaxErrorNew(response);
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

    function checkAddProduct(productSlug, productVariationId = null) {
        var elm = '#tableProduct tbody tr.item-product' + '.product-' + productSlug;
        if (productVariationId) {
            elm += '.product-variation-' + productVariationId;
        }
        if ($(elm).length > 0) {
            return true;
        }
        return false;
    }

    function reloadTotalOrder() {
        $.ajax({
            type: "GET",
            url: '{{ route('admin.order.calculate_total_before_save_order') }}',
            data: $('#formOrder').serialize(),
            success: function(response) {
                $("#tableTotalOrder").replaceWith(response.data);
            },
            error: function(response) {
                handleAjaxErrorNew(response);
            }
        })
    }

    function deleteItemOrderDetail(id, elm) {
        $.ajax({
            type: "DELETE",
            url: '{{ route('admin.order_detail.delete', '') }}' + '/' + id,
            data: {
                _token: token
            },
            success: function(response) {
                removeElmItemOrderDetail(elm);
            },
            error: function(response) {
                handleAjaxErrorNew(response);
            }
        })
    }

    function removeElmItemOrderDetail(elm) {
        $(elm).parents('.item-product').remove();
        if ($('#tableProduct tbody tr.item-product').length == 0) {
            $('#tableProduct tbody tr.no-product').show();
        }
    }
    $(document).on('click', '.remove-item-product', function(e) {
        var id = $(this).data('id'),
            that = this;
        Swal.fire({
            title: "Bạn có chắc chắn muốn thực hiện?",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#1c5639",
            cancelButtonColor: "#d33",
            confirmButtonText: '<i class="ti ti-check"></i> Xác nhận',
            cancelButtonText: '<i class="ti ti-x"></i> Quay lại',
        }).then((result) => {
            if (result.isConfirmed) {
                if (id) {
                    deleteItemOrderDetail(id, that);
                } else {
                    removeElmItemOrderDetail(that)
                }
                reloadTotalOrder();
            }
        });
    })
    $(document).ready(function(e) {
        select2LoadData($('#user_id').data('url'), '#user_id');
        select2LoadData($('#province_id').data('url'), '#province_id');
        select2LoadData($('#admin_id').data('url'), '#admin_id');
        searchProduct('', '#showSearchResultProduct');

        provinceId = $('#province_id').val();
        let urlWard = "{{ route('admin.search.select.ward') }}";
        select2LoadData(urlWard + '?province_id=' + provinceId, '#ward_id');
        $("#inputSearchProduct").keyup($.debounce(500, function(e) {
            searchProduct($(this).val(), '#showSearchResultProduct');
        }));
    });
    $(document).on('click', '.add-product', function(e) {
        var that = $(this),
            productSlug = that.data('product-slug');

        // Check if button is already disabled (request in progress)
        if (that.hasClass('disabled') || that.prop('disabled')) {
            return;
        }

        if (checkAddProduct(productSlug)) {
            showToastify('error', 'Lỗi', 'Sản phẩm này đã được thêm');
            return;
        }

        addProduct({
            product_slug: productSlug
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

        if (checkAddProduct(productSlug, productVariationId)) {
            showToastify('error', 'Lỗi', 'Sản phẩm này đã được thêm');
            return;
        }

        addProduct({
            product_slug: productSlug,
            product_variation_id: productVariationId,
        }, that);
    })

    $(document).on('change', 'select[name="order[user_id]"]', function(e) {
        var url = "{{ route('admin.order.render_info_shipping') }}",
            userId = $(this).val();
        if (userId) {
            $.ajax({
                type: "GET",
                url: url,
                data: {
                    user_id: userId
                },
                success: function(response) {
                    $("#infoShipping").html(response);
                },
                error: function(response) {
                    handleAjaxError(response);
                }
            })
        }
    });

    $(document).on('change', 'select[name="order[province_id]"]', function(e) {
        provinceId = $(this).val();
        let urlWard = "{{ route('admin.search.select.ward') }}";
        select2LoadData(urlWard + '?province_id=' + provinceId, '#ward_id');
        $('#ward_id').val(null).trigger('change');
    });

    $(document).on('change', 'input[name="order_detail[product_qty][]"]', $.debounce(300, function(e) {
        var unitPrice = $(this).parents('.item-product').find('td.unit-price').text();
        unitPrice = unitPrice.replace(/[$,]/g, '');
        unitPrice = parseFloat(unitPrice);
        unitPrice = unitPrice * $(this).val();
        $(this).parents('.item-product').find('td.total-price').text(formatPriceNoCurrency(unitPrice));
        reloadTotalOrder();
    }))
</script>
