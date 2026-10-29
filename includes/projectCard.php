<article class="project-card project-exhibit" data-project-card data-project-id="<?= escapeHtml($project['id']) ?>" data-category="<?= escapeHtml($project['category']) ?>" aria-labelledby="<?= escapeHtml($project['id']) ?>-title">
    <div class="project-flip-card">
        <div class="project-flip-inner">
            <button class="project-flip-front" type="button" data-project-flip aria-expanded="false" aria-label="Show details for <?= escapeHtml($project['title']) ?>">
                <img src="<?= escapeHtml($project['images'][0]['src']) ?>" alt="<?= escapeHtml($project['images'][0]['alt']) ?>" width="<?= (int) ($project['images'][0]['width'] ?? 800) ?>" height="<?= (int) ($project['images'][0]['height'] ?? 500) ?>" loading="lazy">
            </button>
            <div class="project-flip-back">
                <p class="mini-label"><?= escapeHtml($project['categoryLabel']) ?> PROJECT</p>
                <h4><?= escapeHtml($project['title']) ?></h4>
                <p><?= escapeHtml($project['description']) ?></p>
                <a class="button button-primary project-view-link" href="project.php?project=<?= rawurlencode($project['id']) ?>" aria-label="View <?= escapeHtml($project['title']) ?> project">View Project</a>
                <button class="project-unflip" type="button" data-project-unflip>Back to image</button>
            </div>
        </div>
    </div>
    <div class="project-content">
        <div class="project-category"><span class="category-dot<?= $project['category'] === 'design' ? ' violet-dot' : '' ?>"></span><?= escapeHtml($project['categoryLabel']) ?></div>
        <h3 id="<?= escapeHtml($project['id']) ?>-title"><?= escapeHtml($project['title']) ?></h3>
        <p class="project-description"><?= escapeHtml($project['description']) ?></p>
        <div class="project-exhibit-meta">
            <div class="technology-logos" role="group" aria-label="Technologies used">
                <?php foreach ($project['technologies'] as $technology): ?>
                    <?php $technologyIconPath = getTechnologyIconPath($technology); ?>
                    <span class="technology-logo" title="<?= escapeHtml($technology) ?>">
                        <?php if ($technologyIconPath !== null): ?><img src="<?= escapeHtml($technologyIconPath) ?>" alt="<?= escapeHtml($technology) ?>" width="24" height="24" loading="lazy"><?php else: ?><span><?= escapeHtml($technology) ?></span><?php endif; ?>
                    </span>
                <?php endforeach; ?>
            </div>
            <span><?= count($project['images']) ?> images</span>
        </div>
    </div>
</article>
