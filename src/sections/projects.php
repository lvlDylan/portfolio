<?php

require_once __DIR__ . "/../database/database.php";

$sql = "SELECT 
    p.id, 
    p.title, 
    p.description, 
    p.github_link,
    GROUP_CONCAT(s.name ORDER BY s.id SEPARATOR ', ') AS stack_names,
    GROUP_CONCAT(s.icon_name ORDER BY s.id SEPARATOR ',') AS stack_icons,
    GROUP_CONCAT(s.color_name ORDER BY s.id SEPARATOR ',') AS stack_colors
FROM projects p
LEFT JOIN project_stacks ps ON p.id = ps.project_id
LEFT JOIN stacks s ON ps.stack_id = s.id
GROUP BY p.id
ORDER BY p.id;
";

$stmt = isset($pdo) ? $pdo->query($sql) : null;
$projects = isset($stmt) ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<section id="projets" class="container-fluid py-5">
    <div class="container">
        <h2 class="mb-5 fw-bold display-5 border-bottom pb-3 text-gradient">Mes Projets</h2>
        <h3 class="fw-semibold display-6 text-center">Retrouvez l'intégralité de mes travaux sur mon
            <a href="https://github.com/lvlDylan" target="_blank" class="text-decoration-none"
               rel="noopener noreferrer">
                Github
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="currentColor"
                     class="bi bi-github" viewBox="0 0 16 16">
                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/>
                </svg>
            </a>
        </h3>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 card-hover">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= $project["title"] ?></h5>
                            <p class="card-text text-muted"><?= $project["description"] ?></p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 my-3 ms-2">
                            <?php
                            if (!empty($project['stack_icons'])):
                                $icons = explode(',', $project['stack_icons']);
                                $names = explode(', ', $project['stack_names']);
                                $colors = explode(',', $project['stack_colors']);

                                foreach ($icons as $index => $iconClass):
                                    $name = $names[$index];
                                    $colorClass = $colors[$index];
                                    ?>
                                    <span class="tech-badge bg-<?= htmlspecialchars($colorClass) ?>">
                                        <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                                        <?= htmlspecialchars($name) ?>
                                    </span>
                                <?php
                                endforeach;
                                endif;
                                ?>
                        </div>
                        <div class="card-footer">
                            <a href="https://github.com/lvlDylan/elodieNotifier"
                               class="btn btn-outline-primary w-100 stretched-link" target="_blank"
                               rel="noopener noreferrer">
                                Voir sur Github
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                     class="bi bi-github" viewBox="0 0 16 16">
                                    <path d="M8 0C3.58 0 0 3.58 0 8c0 3.54 2.29 6.53 5.47 7.59.4.07.55-.17.55-.38 0-.19-.01-.82-.01-1.49-2.01.37-2.53-.49-2.69-.94-.09-.23-.48-.94-.82-1.13-.28-.15-.68-.52-.01-.53.63-.01 1.08.58 1.23.82.72 1.21 1.87.87 2.33.66.07-.52.28-.87.51-1.07-1.78-.2-3.64-.89-3.64-3.95 0-.87.31-1.59.82-2.15-.08-.2-.36-1.02.08-2.12 0 0 .67-.21 2.2.82.64-.18 1.32-.27 2-.27s1.36.09 2 .27c1.53-1.04 2.2-.82 2.2-.82.44 1.1.16 1.92.08 2.12.51.56.82 1.27.82 2.15 0 3.07-1.87 3.75-3.65 3.95.29.25.54.73.54 1.48 0 1.07-.01 1.93-.01 2.2 0 .21.15.46.55.38A8.01 8.01 0 0 0 16 8c0-4.42-3.58-8-8-8"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
</section>