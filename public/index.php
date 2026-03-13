<?php
include_once "../src/config.php";
include "../src/components/head.php";
?>

<body data-bs-spy="scroll" data-bs-target="#desktop-nav" data-bs-offset="50">

<?php
include "../src/components/navbar-mobile.php";
include "../src/components/sidebar.php";
?>

<main class="main-content mt-lg-3">
    <?php
    include __DIR__ . '/../src/sections/hero.php';
    include __DIR__ . '/../src/sections/projects.php';
    include __DIR__ . '/../src/sections/contact.php';
    ?>

    <?php include __DIR__ . '/../src/components/footer.php'; ?>
    <?php include __DIR__ . '/../src/components/legal-modal.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
</main>
</body>

