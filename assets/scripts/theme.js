const savedTheme = localStorage.getItem('theme'); // Get theme
const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches; // Get system preferences
if (savedTheme) { // If it has theme, set it
    document.documentElement.setAttribute('data-bs-theme', savedTheme);
} else {
    document.documentElement.setAttribute('data-bs-theme', systemPrefersDark ? 'dark' : 'light'); // Set system preference
}

/**
 * @author Dylan Lavieille
 * @function
 *
 * @description Switch current theme in localStorage and set it in HTML attribute page
 */
function switchTheme() {
    const htmlElement = document.documentElement;
    const currentTheme = htmlElement.getAttribute('data-bs-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    htmlElement.setAttribute('data-bs-theme', newTheme);
    if (newTheme === 'dark')
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#0f172a');
    else
        document.querySelector('meta[name="theme-color"]').setAttribute('content', '#f8fafc');

    localStorage.setItem('theme', newTheme);
}

document.addEventListener('DOMContentLoaded', () => {
    // Get "Changer Theme" button
    const btnDesktop = document.getElementById('theme-toggle');
    const btnMobile = document.getElementById('theme-toggle-mobile');

    // Check if they exist then add event and call switchTheme when event is fired
    if (btnDesktop) {
        btnDesktop.addEventListener('click', switchTheme);
    }
    if (btnMobile) {
        btnMobile.addEventListener('click', switchTheme);
    }
});