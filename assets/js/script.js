document.addEventListener('DOMContentLoaded', function () {
    var flash = document.querySelector('.notice, .error');
    if (flash) {
        setTimeout(function () {
            flash.style.opacity = '0';
            flash.style.transition = 'opacity 0.4s ease';
        }, 2500);
    }

    var toggles = document.querySelectorAll('.edukasi-toggle');
    toggles.forEach(function (toggle) {
        toggle.addEventListener('click', function () {
            var card = toggle.closest('.edukasi-card');
            if (!card) {
                return;
            }

            var expanded = card.classList.toggle('is-expanded');
            toggle.setAttribute('aria-expanded', expanded ? 'true' : 'false');
            toggle.textContent = expanded ? 'Sembunyikan' : 'Baca Selengkapnya';
        });
    });
});
