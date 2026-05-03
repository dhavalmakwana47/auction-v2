/* =============================================
   Roles Module - List Page Scripts
   public/app/roles/list.js
   ============================================= */

$(function () {

    $('#roles-table').DataTable({
        processing: false,
        serverSide: true,
        responsive: true,
        ajax: rolesConfig.datatableUrl,
        preDrawCallback: function () {
            $('#roles-loader').show();
        },
        drawCallback: function () {
            $('#roles-loader').hide();
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false, width: '50px' },
            { data: 'name' },
            { data: 'permissions', orderable: false },
            { data: 'action',      orderable: false, searchable: false, className: 'text-center' },
        ],
        pageLength: 10,
        dom: '<"row align-items-center mb-3"<"col-sm-6"l><"col-sm-6 text-right"f>>rt<"row align-items-center mt-3"<"col-sm-6"i><"col-sm-6"p>>',
        language: {
            search: '',
            searchPlaceholder: 'Search roles...',
            lengthMenu: 'Show _MENU_ entries',
            emptyTable: '<div class="text-center py-4"><i class="fas fa-user-tag fa-3x text-muted mb-3 d-block"></i><p class="text-muted">No roles found</p></div>',
        }
    });

    $(document).on('click', '.btn-delete', function () {
        var url = $(this).data('url');
        Swal.fire({
            title: 'Delete Role?',
            text: 'This action cannot be undone.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e3342f',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete!',
            cancelButtonText: 'Cancel',
        }).then(function (result) {
            if (result.isConfirmed) {
                $('<form method="POST"><input name="_token" value="' + rolesConfig.csrfToken + '"><input name="_method" value="DELETE"></form>')
                    .attr('action', url)
                    .appendTo('body')
                    .submit();
            }
        });
    });

});
