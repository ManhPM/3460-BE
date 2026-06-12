<x-input type="hidden" name="route_create_attribute" :value="route('admin.product.attribute.create')" />
<x-input type="hidden" name="route_check_create_variation" :value="route('admin.product.variation.check')" />
<script>
    //xóa item attribute
    $(document).on('click', '.remove-attribute-item', function(e) {
        e.preventDefault();
        Swal.fire({
            title: "Bạn có chắc chắn muốn xóa thuộc tính này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#1c5639",
            cancelButtonColor: "#d33",
            confirmButtonText: '<i class="ti ti-check"></i> Xác nhận',
            cancelButtonText: '<i class="ti ti-x"></i> Quay lại',
        }).then((result) => {
            if (!result.isConfirmed) {
                return;
            }

            var that = $(this),
                attrbute = that.parents('.wrap-item-attribute'),
                attributeId = $(attrbute[0]).data('attribute-id'),
                urlDelete = that.data('product-attribute-delete-route'),
                flag = true;

            if (!urlDelete == 0 && !urlDelete == '') {
                flag = deleteAjaxProductData(urlDelete);
            }

            if (flag) {
                $(selectAttribute).find('option[value="' + attributeId + '"]').removeAttr('disabled')
                attrbute.remove();
            }
        });
        return;
        var that = $(this),
            attrbute = that.parents('.wrap-item-attribute'),
            attributeId = $(attrbute[0]).data('attribute-id'),
            urlDelete = that.data('product-attribute-delete-route'),
            flag = true;

        if (!urlDelete == 0 && !urlDelete == '') {
            flag = deleteAjaxProductData(urlDelete);
        }

        if (flag) {
            $(selectAttribute).find('option[value="' + attributeId + '"]').removeAttr('disabled')
            attrbute.remove();
        }
    });

    // render view variation
    $(document).on('click', '#btnSaveAttribute', function(e) {
        cloneInputAttribute(formCreateVariation);
        $(formCreateVariation).trigger('submit');
    });

    //submit form from attribute check create variation
    $(formCreateVariation).submit(function(e) {
        e.preventDefault();
        var form = $(this),
            url = $('input[name="route_check_create_variation"]').val();
        $.ajax({
            type: "GET",
            url: url,
            data: form.serialize(),
            processData: false,
            contentType: false,
            success: function(response) {
                $("#variations").html(response);
                reorderElement();
                showToastify('success', 'Thành công',
                    'Thêm thuộc tính thành công, hãy chuyển qua tab "Các biến thể" chọn "Tạo biến thể từ tất cả thuộc tính".'
                );
            },
            error: function(response) {
                handleAjaxErrorNew(response);
                $("#variations").html(response.responseJSON.viewError);
            }
        })
    });


    // them moi thuoc tinh tu select
    $(document).on('click', '#btnAttributeAddNew', function(e) {
        e.preventDefault();
        var attribute_id = $(selectAttribute).val()
        url = $('input[name="route_create_attribute"]').val(),
            hasAttribute = $('.wrap-item-attribute[data-attribute-id="' + attribute_id + '"]').length;

        if (!attribute_id) {
            showToastify('error', 'Lỗi', 'Vui lòng chọn giá trị!');
            return;
        } else if (hasAttribute > 0) {
            showToastify('error', 'Lỗi', 'Giá trị này đã được chọn trước đó');
            return;
        }
        $.ajax({
            type: "GET",
            url: url,
            data: {
                attribute_id: attribute_id
            },
            success: function(response) {

                $("#listAttribute").append(response); // render list attribute
                $(selectAttribute).prop('selectedIndex', 0); // reset select attribute
                // disable option select attribute
                $(selectAttribute).find('option[value="' + attribute_id + '"]').attr('disabled',
                    'disabled');
                addSelect2();
            },
            error: function(response) {
                handleAjaxErrorNew(response);
            }
        });
    });
</script>
