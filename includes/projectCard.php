<article class="project-card project-cover-card" data-project-card data-project-id="<?= escapeHtml($project['id']) ?>" data-category="<?= escapeHtml($project['category']) ?>" aria-labelledby="<?= escapeHtml($project['id']) ?>-title" data-reveal>
    <div class="project-cover">
        <img src="<?= escapeHtml($project['images'][0]['src']) ?>" alt="<?= escapeHtml($project['images'][0]['alt']) ?>" width="<?= (int) ($project['images'][0]['width'] ?? 800) ?>" height="<?= (int) ($project['images'][0]['height'] ?? 500) ?>" loading="lazy" decoding="async">
        <div class="project-cover-action">
            <a class="project-view-link" href="project.php?project=<?= rawurlencode($project['id']) ?>" aria-label="View <?= escapeHtml($project['title']) ?> project">View Project</a>
        </div>
    </div>
    <div class="project-content">
        <div class="project-category"><span class="category-dot<?= $project['category'] === 'design' ? ' violet-dot' : '' ?>"></span><?= escapeHtml($project['categoryLabel']) ?><span class="project-number"><?= str_pad((string) ($projectIndex + 1), 2, '0', STR_PAD_LEFT) ?></span></div>
        <h3 id="<?= escapeHtml($project['id']) ?>-title"><?= escapeHtml($project['title']) ?></h3>
        <p class="project-description"><?= escapeHtml($project['description']) ?></p>
        <div class="project-cover-meta">
            <div class="technology-logos" role="group" aria-label="Technologies used">
                <?php foreach ($project['technologies'] as $technology): ?>
                    <?php $technologyIconPath = getTechnologyIconPath($technology); ?>
                    <span class="technology-logo" title="<?= escapeHtml($technology) ?>">
                        <?php if ($technologyIconPath !== null): ?>
                            <img src="<?= escapeHtml($technologyIconPath) ?>" alt="<?= escapeHtml($technology) ?>" width="24" height="24" loading="lazy" decoding="async">
                        <?php else: ?>
                            <span class="technology-fallback"><?= escapeHtml($technology) ?></span>
                        <?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <span class="project-screen-count"><?= count($project['images']) ?> images to explore</span>
        </div>
    </div>
</article>
