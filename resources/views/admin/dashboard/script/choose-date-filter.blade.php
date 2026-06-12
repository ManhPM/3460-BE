<script>
    document.addEventListener("DOMContentLoaded", function() {
        const datePreset = document.getElementById("datePreset");
        const fromDate = document.getElementById("from_date");
        const toDate = document.getElementById("to_date");

        function formatDate(date) {
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const year = date.getFullYear();
            return `${day}-${month}-${year}`;
        }

        datePreset.addEventListener("change", function() {
            const today = new Date();
            let start, end;

            switch (this.value) {
                case "today":
                    start = end = today;
                    break;
                case "yesterday":
                    start = end = new Date(today.setDate(today.getDate() - 1));
                    break;
                case "this_week": {
                    const day = today.getDay() || 7; // Chủ nhật = 7
                    start = new Date(today);
                    start.setDate(today.getDate() - day + 1);
                    end = new Date();
                    break;
                }
                case "last_week": {
                    const day = today.getDay() || 7;
                    end = new Date(today);
                    end.setDate(today.getDate() - day);
                    start = new Date(end);
                    start.setDate(end.getDate() - 6);
                    break;
                }
                case "this_month":
                    start = new Date(today.getFullYear(), today.getMonth(), 1);
                    end = new Date(today.getFullYear(), today.getMonth() + 1, 0);
                    break;
                case "this_year":
                    start = new Date(today.getFullYear(), 0, 1);
                    end = new Date(today.getFullYear(), 11, 31);
                    break;
                case "last_year":
                    start = new Date(today.getFullYear() - 1, 0, 1);
                    end = new Date(today.getFullYear() - 1, 11, 31);
                    break;
                default:
                    if (this.value.startsWith("month_")) {
                        const month = parseInt(this.value.split("_")[1], 10) - 1;
                        start = new Date(today.getFullYear(), month, 1);
                        end = new Date(today.getFullYear(), month + 1, 0);
                    } else if (this.value.startsWith("quarter_")) {
                        const q = parseInt(this.value.split("_")[1], 10);
                        const monthStart = (q - 1) * 3;
                        start = new Date(today.getFullYear(), monthStart, 1);
                        end = new Date(today.getFullYear(), monthStart + 3, 0);
                    } else {
                        start = end = null;
                    }
            }

            if (start && end) {
                fromDate.value = formatDate(start);
                toDate.value = formatDate(end);
            } else {
                fromDate.value = "";
                toDate.value = "";
            }
        });

        // nút reset
        document.getElementById("resetFilter").addEventListener("click", function() {
            datePreset.value = "";
            fromDate.value = "";
            toDate.value = "";
        });
    });
</script>
