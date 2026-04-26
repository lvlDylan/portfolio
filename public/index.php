<?php
require_once __DIR__ . "/../src/config.php";
require_once __DIR__ . "/../src/database/database.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['action']) && $_POST['action'] == "send_contact") {
        require_once __DIR__ . "/../src/action/email_sender.php";
        exit();
    }
}

include "../src/components/head.php";
?>

<body data-bs-spy="scroll" data-bs-target="#desktop-nav" data-bs-offset="50">

<?php
include __DIR__ . "/../src/components/navbar-mobile.php";
include __DIR__ . "/../src/components/sidebar.php";
?>

<main class="main-content mt-lg-3">
    <?php
    include __DIR__ . '/../src/sections/hero.php';
    include __DIR__ . '/../src/sections/skills.php';
    include __DIR__ . '/../src/sections/projects.php';
    include __DIR__ . '/../src/sections/contact.php';
    ?>

    <?php include __DIR__ . '/../src/components/footer.php'; ?>
    <?php include __DIR__ . '/../src/components/modal/legal-modal.php'; ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

<script src="assets/js/contact.js"></script>

<script>
    AOS.init({
        duration: 1000,
        easing: 'ease-in-out',
        once: true,
        mirror: false,
        offset: 100,
    });
</script>
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

</body>

