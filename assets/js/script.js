document.addEventListener('DOMContentLoaded', function () {
    var flash = document.querySelector('.notice, .error');
    if (flash) {
        setTimeout(function () {
            flash.style.opacity = '0';
            flash.style.transition = 'opacity 0.4s ease';
        }, 2500);
    }
});
