<script>
    function checkEndDate(input) {
        const endDate = new Date(input.value);
        const currentDate = new Date();

        if (endDate < currentDate) {
            showToastify('error', 'Lỗi', 'Ngày kết thúc phải lớn hơn ngày hiện tại!');
            input.value = '';
            return;
        }
    }

    function checkDiscountValue(input) {
        const type = discountValueType.value;
        let value = input.value.replace(/( VND| %)/g, "").trim(); // Xóa định dạng hiển thị

        if (type == {{ App\Enums\Discount\DiscountValueType::Percent->value }}) {
            value = parseInt(value);
            if (isNaN(value) || value < 0 || value > 100) {
                showToastify('error', 'Lỗi', 'Giá trị phải lớn hơn 0 và nhỏ hơn 100 khi loại giảm là phần trăm!');
                input.value = ''; // Xóa giá trị không hợp lệ
                return;
            }
            input.value = value + " %"; // Thêm định dạng %
        } else if (type == {{ App\Enums\Discount\DiscountValueType::Money->value }}) {
            value = parseInt(value);
            if (isNaN(value) || value < 0) {
                showToastify('error', 'Lỗi', 'Giá trị giảm phải lớn hơn 0 khi loại giảm là số tiền cố định!');
                input.value = ''; // Xóa giá trị không hợp lệ
                return;
            }
            input.value = value.toLocaleString() + " VND"; // Thêm định dạng VND
        }
    }


    $(document).ready(function() {
        select2LoadData($('#user_id').data('url'), '#user_id');
        $('select[name="type"]').on('change', function() {
            $('input[name="discount_value"]').val('');
        });
    });
</script>
