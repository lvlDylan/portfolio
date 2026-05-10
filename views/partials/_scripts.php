<script>
    const htmlElement = document.documentElement;
    const savedTheme = localStorage.getItem('theme') || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
    const themeToggles = document.querySelectorAll('.theme-toggle-checkbox');

    htmlElement.setAttribute('data-bs-theme', savedTheme);

    themeToggles.forEach(toggle => {
        toggle.checked = (savedTheme === 'dark');
        toggle.addEventListener('change', () => {
            const newTheme = toggle.checked ? 'dark' : 'light';

            htmlElement.setAttribute('data-bs-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            themeToggles.forEach(t => t.checked = toggle.checked);
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script src="https://unpkg.com/typed.js@3.0.0/dist/typed.umd.js"></script>

<script>
    new Typed('#typing-text', {
        strings: [
            "recherche un stage du 26 mai au 4 juillet."
        ],
        typeSpeed: 50,
        backSpeed: 25,
        loop: false,
        backDelay: 3000,
        startDelay: 500,
        showCursor: true,
        cursorChar: '|',
    });

    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
        offset: 100,
    });
</script>

<script src="scripts/contact.js"></script>