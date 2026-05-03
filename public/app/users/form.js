/* =============================================
   Users Module - Form Page Scripts
   public/app/users/form.js
   ============================================= */

function togglePass(id, el) {
    var input = document.getElementById(id);
    var icon  = el.querySelector('i');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
