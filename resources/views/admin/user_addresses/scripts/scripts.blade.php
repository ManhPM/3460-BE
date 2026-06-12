<script>
    $(document).ready(function(e) {
        $("#emailInput").on("input", function() {
            $("#emailDeliInput").val($(this).val());
        });
        select2LoadData($('#province_id').data('url'), '#province_id');
        select2LoadData($('#discount_id').data('url'), '#discount_id');

        let provinceId;

        provinceId = $('#province_id').val();

        let urlWard = "{{ route('admin.search.select.ward') }}";
        select2LoadData(urlWard + '?province_id=' + provinceId, '#ward_id');
    });

    $(document).on('change', 'select[name="province_id"]', function(e) {
        provinceId = $(this).val();
        let url = "{{ route('admin.search.select.ward') }}";
        select2LoadData(url + '?province_id=' + provinceId, '#ward_id');
        $('#ward_id').val(null).trigger('change');
    });
</script>
