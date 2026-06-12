<x-input type="hidden" name="id_table" :value="$id_table" />
<script>
    $(document).ready(function() {
        // define columns for the datatables
        columns = window.LaravelDataTables[$("input[name=id_table]").val()].columns();
        toggleColumnsDatatable(columns);

        const tableId = '#{{ $dataTable->getTableAttribute('id') }}';

        $(tableId + ' tbody').on('change', 'input[type="checkbox"]', function() {
            const row = $(this).closest('tr');
            if (this.checked) {
                row.addClass('selected-row');
            } else {
                row.removeClass('selected-row');
            }
        });

        $(tableId + ' thead').on('change', '.check-all', function() {
            const checked = this.checked;
            $(tableId + ' tbody input[type="checkbox"]').prop('checked', checked).trigger('change');
        });

        $(tableId + ' tbody').on('change', 'input[type="checkbox"]', function() {
            const allChecked = $(tableId + ' tbody input[type="checkbox"]:not(:checked)').length === 0;
            $(tableId + ' thead .check-all').prop('checked', allChecked);
        });
    });
</script>
