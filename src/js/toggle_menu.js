document.addEventListener("DOMContentLoaded", function() {
    const menuToggle = document.getElementById('menu-toggle');
    const menu = document.getElementById('menu').querySelector('ul');

    menuToggle.addEventListener('click', function() {
        menu.classList.toggle('show');
    });
});