const savedTheme = localStorage.getItem('theme');
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
if (savedTheme) {
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
} else {
    document.documentElement.setAttribute('data-bs-theme', systemPrefersDark ? 'dark' : 'light');
}

function switchTheme() {
    const htmlElement = document.documentElement;
    const currentTheme = htmlElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    htmlElement.setAttribute('data-bs-theme', newTheme);
    localStorage.setItem('theme', newTheme);
}

document.addEventListener('DOMContentLoaded', () => {
    const btnDesktop = document.getElementById('theme-toggle');
    const btnMobile = document.getElementById('theme-toggle-mobile');

    if (btnDesktop) {
        btnDesktop.addEventListener('click', switchTheme);
    }
    if (btnMobile) {
        btnMobile.addEventListener('click', switchTheme);
    }

    const navLinks = document.querySelectorAll('.nav-link');

    navLinks.forEach((link) => {
        link.addEventListener('click', (e) => {
            navLinks.forEach(nav => nav.classList.remove('active'));
            e.target.classList.add('active');
        })
    })
});