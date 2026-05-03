/* =============================================
   Auctions Module - List Page Scripts
   public/app/auctions/list.js
   ============================================= */

$(function () {

    $('#auctions-table').DataTable({
        processing: false,
        serverSide: true,
        responsive: true,
        ajax: auctionsConfig.datatableUrl,
        preDrawCallback: function () { $('#auctions-loader').show(); },
        drawCallback:    function () { $('#auctions-loader').hide(); },
        columns: [
            { data: 'DT_RowIndex',          orderable: false, searchable: false, width: '50px' },
            { data: 'corporate_debtor_name' },
            { data: 'meeting_date' },
            { data: 'base_price' },
            { data: 'initial_npv_value', orderable: false },
            { data: 'increment_type',  orderable: false },
            { data: 'action',          orderable: false, searchable: false, className: 'text-center' },
        ],
        pageLength: 10,
        dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
        language: {
            search: '',
            searchPlaceholder: 'Search auctions...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: '<div class="text-center py-4"><i class="fas fa-gavel fa-3x text-muted mb-3 d-block"></i><p class="text-muted">No auctions found</p></div>',
        }
    });

    $(document).on('click', '.btn-delete', function () {
        var url = $(this).data('url');
        Swal.fire({
            title: 'Delete Auction?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                $('<form method="POST"><input name="_token" value="' + auctionsConfig.csrfToken + '"><input name="_method" value="DELETE"></form>')
                    .attr('action', url).appendTo('body').submit();
            }
        });
    });

});
