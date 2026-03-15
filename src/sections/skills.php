<?php

$sql = "SELECT 
    category, 
    GROUP_CONCAT(name ORDER BY id SEPARATOR ', ') AS stack_names,
    GROUP_CONCAT(color_name ORDER BY id SEPARATOR ',') AS stack_colors,
    GROUP_CONCAT(icon_name ORDER BY id SEPARATOR ',') AS stack_icons
FROM stacks 
GROUP BY category 
ORDER BY FIELD(category, 'frontend', 'backend', 'software-sys');";

$stmt = isset($pdo) ? $pdo->query($sql) : null;
$skills = isset($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

$display_titles = [
        'frontend'     => 'Frontend',
        'backend'      => 'Backend',
        'software-sys' => 'Logiciel & Système'
];

$display_descriptions = [
        'frontend'     => 'Interfaces web réactives et typage statique.',
        'backend'      => 'Logique serveur, API et bases de données SQL.',
        'software-sys' => 'Développement applicatif, bas niveau et automatisation.'
];

$display_icons = [
        'frontend'     => 'bi-window',
        'backend'      => 'bi-database-gear',
        'software-sys' => 'bi-cpu'
];
?>

<section id="skills" class="py-5">
    <?php $delay = 0; ?>
    <div class="container">
        <h2 class="mb-5 fw-bold display-5 border-bottom pb-3 text-gradient">Mes Compétences</h2>
        <div class="row g-4">
            <?php foreach ($skills as $skill): ?>
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 card-hover card-skill <?= $skill['category']; ?>" data-aos="fade-up" data-aos-delay="<?= $delay ?>">
                    <div class="card-body">
                        <h5 class="card-title fw-bold">
                            <i class="bi <?= $display_icons[$skill['category']] ?> me-2"></i>
                            <?= htmlspecialchars($display_titles[$skill["category"]]); ?></h5>
                        <p class="card-text text-muted small"><?= htmlspecialchars($display_descriptions[$skill["category"]]); ?></p>
                    </div>
                    <div class="d-flex flex-wrap gap-2 my-3 ms-2">
                        <?php
                        $names = explode(', ', $skill['stack_names']);
                        $colors = explode(',', $skill['stack_colors']);
                        $icons = explode(',', $skill['stack_icons']);

                        foreach ($names as $index => $name): ?>
                            <span class="tech-badge bg-<?= htmlspecialchars($colors[$index]); ?>">
                                    <i class="<?= htmlspecialchars($icons[$index]); ?>"></i>
                                    <?= htmlspecialchars($name); ?>
                                </span>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php $delay += 200; endforeach; ?>
        </div>
    </div>
</section>