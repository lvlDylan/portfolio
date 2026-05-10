<?php
/**
 * @var string $title Titre de la page
 * @var string $content Contenu injecté par le contrôleur principal
 */
?>

<!doctype html>
<html lang="fr">
<head>
    <title><?= $title ?></title>
    <?php require_once ROOT . "/views/partials/_head.php"; ?>
</head>
<body data-bs-spy="scroll" data-bs-target="#desktop-nav" data-bs-offset="50">

<?php require_once ROOT . "/views/partials/_mobile_menu.php"; ?>
<?php require_once ROOT . "/views/partials/_sidebar.php"; ?>

<main class="main-content mt-lg-3">
    <?= $content ?>
</main>

<?php require_once ROOT . "/views/partials/_footer.php"; ?>

</body>
</html>