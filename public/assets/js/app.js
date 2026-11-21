(function () {
    'use strict';

    const menuToggle = document.querySelector('[data-menu-toggle]');
    const sidebar = document.querySelector('#sidebar');

    if (menuToggle && sidebar) {
        menuToggle.addEventListener('click', function () {
            sidebar.classList.toggle('is-open');
        });

        document.addEventListener('click', function (event) {
            if (window.innerWidth <= 800 &&
                sidebar.classList.contains('is-open') &&
                !sidebar.contains(event.target) &&
                !menuToggle.contains(event.target)) {
                sidebar.classList.remove('is-open');
            }
        });
    }

    const skuInput = document.querySelector('[data-sku-input]');
    if (skuInput) {
        skuInput.addEventListener('input', function () {
            skuInput.value = skuInput.value.toUpperCase().replace(/\s+/g, '-');
        });
    }

    document.querySelectorAll('.alert-success').forEach(function (alert) {
        window.setTimeout(function () {
            alert.classList.add('is-hidden');
        }, 6000);
    });
}());
