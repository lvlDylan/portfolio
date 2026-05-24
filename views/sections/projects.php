<?php /** @var $projects array */ ?>
<section id="projets" class="container-fluid py-5">
    <div class="container">
        <h2 class="mb-5 fw-bold display-5 border-bottom pb-3">Mes Projets</h2>

        <div class="row g-4">
            <?php foreach ($projects as $index => $project): ?>

                <div class="modal fade" id="projectModal<?= $project['id'] ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold"><?= htmlspecialchars($project["title"]) ?></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="lead"><?= nl2br(htmlspecialchars($project["description"])) ?></p>

                                <h6 class="fw-bold mt-4">Technologies utilisées :</h6>
                                <div class="d-flex flex-wrap gap-2 mb-4 mt-2">
                                    <?php foreach ($project['names'] as $i => $name): ?>
                                        <span class="tech-badge bg-<?= htmlspecialchars($project['colors'][$i]) ?>">
                                            <i class="<?= htmlspecialchars($project['icons'][$i]) ?>"></i>
                                            <?= htmlspecialchars($name) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ($project['image_full']): ?>
                                    <img class="img-fluid rounded w-100 shadow-sm"
                                         src="/img/projects/<?= htmlspecialchars($project['image_full']) ?>"
                                         alt="<?= htmlspecialchars($project['title']) ?>"
                                         style="max-height: 400px; object-fit: cover;">
                                <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                                <?php if ($project['github_link']): ?>
                                    <a href="<?= htmlspecialchars($project['github_link']) ?>" target="_blank"
                                       class="btn btn-primary">
                                        <i class="bi bi-github me-2"></i>Voir le code
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 card-hover"
                         data-aos="fade-up"
                         data-aos-delay="<?= $index * 100 ?>">

                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title fw-bold"><?= htmlspecialchars($project["title"]) ?></h5>
                            <p class="card-text text-muted mb-auto small"><?= htmlspecialchars($project["description_shortened"]) ?></p>

                            <div class="d-flex flex-wrap gap-2 my-3">
                                <?php foreach ($project['names'] as $i => $name): ?>
                                    <span class="tech-badge bg-<?= htmlspecialchars($project['colors'][$i]) ?>">
                                        <i class="<?= htmlspecialchars($project['icons'][$i]) ?>"></i>
                                        <span class="d-none d-md-inline"><?= htmlspecialchars($name) ?></span>
                                    </span>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="card-footer bg-transparent border-0 d-flex justify-content-end pb-3">
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary rounded-pill px-3 stretched-link"
                                    data-bs-toggle="modal"
                                    data-bs-target="#projectModal<?= $project['id'] ?>">
                                Détails
                            </button>
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
    </div>
</section>