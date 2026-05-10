<?php /** @var $skills array */?>
<section id="skills" class="py-5">
    <?php $delay = 0; ?>
    <div class="container">
        <h2 class="mb-5 fw-bold display-5 border-bottom pb-3">Mes Compétences</h2>
        <div class="row g-4">
            <?php foreach ($skills as $index => $skill): ?>
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 card-hover card-skill <?= $skill["category"]; ?>" data-aos="fade-up" data-aos-delay="<?= $index * 200 ?>">
                        <div class="card-body">
                            <h5 class="card-title fw-bold">
                                <i class="bi <?= $skill["icon"] ?> me-2"></i>
                                <?= htmlspecialchars($skill["title"]); ?></h5>
                            <p class="card-text text-muted small"><?= htmlspecialchars($skill["description"]); ?></p>
                        </div>
                        <div class="d-flex flex-wrap gap-2 my-3 ms-2">
                            <?php foreach ($skill["names"] as $i => $name): ?>
                                <span class="tech-badge bg-<?= htmlspecialchars($skill["colors"][$i]); ?>">
                                    <i class="<?= htmlspecialchars($skill["icons"][$i]); ?>"></i>
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