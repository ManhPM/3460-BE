<x-input type="hidden" name="route_create_variation" :value="route('admin.product.variation.create')" />
<script>
    function createVariation() {
        var form = $(formCreateVariation),
            url = $('input[name="route_create_variation"]').val();
        $.ajax({
            type: "GET",
            url: url,
            data: form.serialize(),
            processData: false,
            contentType: false,
            success: function(response) {
                $("#listVariation").append(response);
            },
            error: function(response) {
                handleAjaxErrorNew(response);
            }
        })
    }
    $(document).on('click', '#btnVariationAddNew', function(e) {
        e.preventDefault();
        var action = $('select[name="variation_action"]').val();
        if (!action) {
            showToastify('error', 'Lỗi', 'Vui lòng tải lại trang!');
            return;
        }
        var x = document.createElement("INPUT");
        x.setAttribute("type", "hidden");
        x.setAttribute("name", "variation_action");
        x.setAttribute("value", action);
        $(formCreateVariation).append(x);

        createVariation();
    });

    //xóa item biến thể của sản phẩm
    $(document).on('click', '.remove-product-variation-item', function(e) {
        e.preventDefault();
        var that = $(this),
            productVariation = that.parents('.wrap-item-product-variation'),
            urlDelete = that.data('product-variaton-delete-route');

        Swal.fire({
            title: "Bạn có chắc chắn muốn xóa biến thể này?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#1c5639",
            cancelButtonColor: "#d33",
            confirmButtonText: '<i class="ti ti-check"></i> Xác nhận',
            cancelButtonText: '<i class="ti ti-x"></i> Quay lại',
        }).then((result) => {
            if (result.isConfirmed) {
                var flag = true;

                if (!urlDelete == 0 && !urlDelete == '') {
                    flag = deleteAjaxProductData(urlDelete);
                }
                if (flag) {
                    productVariation.remove();
                }
            }
        });
    });
</script>
