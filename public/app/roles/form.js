/* =============================================
   Roles Module - Form Page Scripts
   public/app/roles/form.js
   ============================================= */

$(function () {

    var selectAll = document.getElementById('select_all');
    var checkboxes = document.querySelectorAll('.perm-checkbox');

    if (selectAll) {
        // Set initial state of select-all based on current checked count
        selectAll.checked = checkboxes.length > 0 && checkboxes.length === document.querySelectorAll('.perm-checkbox:checked').length;

        selectAll.addEventListener('change', function () {
            checkboxes.forEach(function (cb) { cb.checked = selectAll.checked; });
        });
    }

    checkboxes.forEach(function (cb) {
        cb.addEventListener('change', function () {
            if (selectAll) {
                selectAll.checked = document.querySelectorAll('.perm-checkbox:checked').length === checkboxes.length;
            }
        });
    });

});
