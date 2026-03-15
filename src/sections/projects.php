<?php

$sql = "SELECT 
    p.id, 
    p.title, 
    p.description, 
    p.github_link,
    p.description_shortened,
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
    <?php $delay = 0; ?>
    <div class="container">
        <h2 class="mb-5 fw-bold display-5 border-bottom pb-3 text-gradient">Mes Projets</h2>
        <div class="row g-4">
            <?php foreach ($projects as $project): ?>
                <div class="modal fade" id="projectModal<?= $project['id'] ?>" tabindex="-1" aria-labelledby="modalLabel<?= $project['id'] ?>" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="modalLabel<?= $project['id'] ?>">
                                    <?= htmlspecialchars($project["title"]) ?>
                                </h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="lead"><?= nl2br(htmlspecialchars($project["description"])) ?></p>

                                <h6 class="fw-bold mt-4">Technologies utilisées :</h6>
                                <div class="d-flex flex-wrap gap-2 mb-4">
                                    <?php
                                    if (!empty($project['stack_icons'])):
                                        $icons = explode(',', $project['stack_icons']);
                                        $names = explode(', ', $project['stack_names']);
                                        $colors = explode(',', $project['stack_colors']);
                                        foreach ($icons as $index => $iconClass): ?>
                                            <span class="tech-badge bg-<?= htmlspecialchars($colors[$index]) ?>">
                                    <i class="<?= htmlspecialchars($iconClass) ?>"></i>
                                    <?= htmlspecialchars($names[$index]) ?>
                                </span>
                                        <?php endforeach;
                                    endif; ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Fermer</button>
                                <a href="<?= htmlspecialchars($project['github_link']) ?>"
                                   target="_blank"
                                   class="btn btn-dark">
                                    Voir le code sur GitHub
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 card-hover" data-aos="fade-right" data-aos-delay="<?= $delay ?>">
                        <div class="card-body">
                            <h5 class="card-title fw-bold"><?= $project["title"] ?></h5>
                            <p class="card-text text-muted"><?= $project["description_shortened"] ?></p>
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
                        <div class="card-footer bg-transparent border-0 d-flex justify-content-end pb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 stretched-link"
                                    data-bs-toggle="modal"
                                    data-bs-target="#projectModal<?= $project["id"]?>">
                                Détails du projet
                            </button>
                        </div>
                    </div>
                </div>
            <?php
            $delay += 100;
            endforeach;
            ?>
        </div>
    </div>
</section>