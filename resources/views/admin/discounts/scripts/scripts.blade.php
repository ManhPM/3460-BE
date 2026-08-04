<script>
    function checkStartDate(input) {
        const endDateInput = document.querySelector('input[name="date_end"]');
        const startDate = new Date(input.value);
        const endDate = new Date(endDateInput.value);

        if (endDate < startDate) {
            showToastify('error', 'Lỗi', 'Ngày bắt đầu không được nhỏ hơn ngày kết thúc!');
            input.value = '';
            return;
        }
    }

    function checkEndDate(input) {
        const startDateInput = document.querySelector('input[name="date_start"]');
        const endDate = new Date(input.value);
        const startDate = new Date(startDateInput.value);
        const currentDate = new Date();

        if (endDate < startDate) {
            showToastify('error', 'Lỗi', 'Ngày kết thúc phải lớn hơn hoặc bằng ngày bắt đầu!');
            input.value = '';
            return;
        }

        if (endDate < currentDate) {
            showToastify('error', 'Lỗi', 'Ngày kết thúc phải lớn hơn ngày hiện tại!');
            input.value = '';
            return;
        }
    }

    function checkDiscountValue(input) {
        const type = discountValueType.value;
        let value = input.value.replace(/( VND| %)/g, "").trim(); // Xóa định dạng hiển thị

        if (type == '{{ App\Enums\Discount\DiscountValueType::Percent->value }}' || type == 'percent') {
            value = parseInt(value);
            if (isNaN(value) || value <= 0 || value > 100) {
                showToastify('error', 'Lỗi', 'Giá trị giảm giá phải từ 1 đến 100 khi loại giảm là phần trăm!');
                input.value = ''; // Xóa giá trị không hợp lệ
                return;
            }
            input.value = value + " %"; // Thêm định dạng %
        } else if (type == '{{ App\Enums\Discount\DiscountValueType::Money->value }}' || type == 'money') {
            value = parseInt(value);
            if (isNaN(value) || value <= 0) {
                showToastify('error', 'Lỗi', 'Giá trị giảm phải lớn hơn 0 khi loại giảm là số tiền cố định!');
                input.value = ''; // Xóa giá trị không hợp lệ
                return;
            }
            input.value = value.toLocaleString() + " VND"; // Thêm định dạng VND
        }
    }


    $(document).ready(function() {
        $('select[name="type"]').on('change', function() {
            $('input[name="discount_value"]').val('');
        });
    });
</script>
