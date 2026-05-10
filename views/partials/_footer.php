<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="liveToast" class="toast hide align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
        <div class="d-flex">
            <div class="toast-body fw-bold" id="toastMessage"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
        <div class="progress" style="height: 3px; border-radius: 0; background-color: rgba(255,255,255,0.3);">
            <div id="toastProgress" class="progress-bar bg-white" role="progressbar"></div>
        </div>
    </div>
</div>

<footer class="py-5 mt-5 border-top">
    <div class="container text-center">
        <p class="fw-medium mb-1">&copy; <?= date('Y') ?> Dylan Lavieille</p>
        <div class="d-flex justify-content-center align-items-center gap-2">
            <span class="text-muted small">Portfolio</span>
            <span class="text-muted">•</span>
            <button type="button" class="btn-legal" data-bs-toggle="modal" data-bs-target="#legalModal">
                <i class="bi bi-shield-lock me-1"></i>Mentions Légales
            </button>
        </div>
    </div>
</footer>

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