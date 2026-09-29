window.toggleLangMenu = function () {
    const menu = document.getElementById('lang-menu');

    if (menu) {
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    }
};

document.addEventListener('click', (event) => {
    const dropdown = document.getElementById('lang-dropdown');
    const menu = document.getElementById('lang-menu');

    if (dropdown && menu && !dropdown.contains(event.target)) {
        menu.style.display = 'none';
    }
});
