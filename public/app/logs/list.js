$(function () {
    var table = $('#logs-table').DataTable({
        processing: false,
        serverSide: true,
        responsive: false,
        scrollX: true,
        autoWidth: false,
        ajax: {
            url: logsConfig.datatableUrl,
            data: function (d) {
                d.event = $('#filter-event').val();
                d.action = $('#filter-action').val();
                d.status_code = $('#filter-status-code').val();
                d.date_from = $('#filter-date-from').val();
                d.date_to = $('#filter-date-to').val();
                d.user_id = $('#filter-user-id').val();
            }
        },
        preDrawCallback: function () {
            $('#logs-loader').show();
        },
        drawCallback: function () {
            $('#logs-loader').hide();
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
            { data: 'user', defaultContent: 'System' },
            { data: 'event_action', orderable: false, searchable: false },
            { data: 'description' },
            { data: 'route_name' },
            { data: 'ip_address' },
            { data: 'status_code' },
            { data: 'occurred_at' },
        ],
        pageLength: 10,
        dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
        language: {
            search: '',
            searchPlaceholder: 'Search logs...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: '<div class="text-center py-4"><i class="fas fa-history fa-3x text-muted mb-3 d-block"></i><p class="text-muted">No logs found</p></div>',
        }
    });

    $('#filter-event, #filter-action, #filter-status-code, #filter-date-from, #filter-date-to, #filter-user-id')
        .on('change keyup', function () {
            table.ajax.reload();
            refreshExportUrl();
        });

    function refreshExportUrl() {
        var params = new URLSearchParams({
            event: $('#filter-event').val() || '',
            action: $('#filter-action').val() || '',
            status_code: $('#filter-status-code').val() || '',
            date_from: $('#filter-date-from').val() || '',
            date_to: $('#filter-date-to').val() || '',
            user_id: $('#filter-user-id').val() || ''
        });

        $('#logs-export-btn').attr('href', logsConfig.exportUrl + '?' + params.toString());
    }

    refreshExportUrl();
});
