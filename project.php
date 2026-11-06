<?php
declare(strict_types=1);

require_once __DIR__ . '/config/appConfig.php';
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/brandLogo.php';
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
$projects = require __DIR__ . '/data/projectData.php';
$requestedProject = is_string($_GET['project'] ?? null) ? $_GET['project'] : '';
$selectedProject = null;
foreach ($projects as $candidateProject) {
    if ($candidateProject['id'] === $requestedProject) {
        $selectedProject = $candidateProject;
        break;
    }
}
$isProjectDetail = true;
$navigationPrefix = 'index.php';
$pageTitle = ($selectedProject['title'] ?? 'Project not found') . ' — Christopher John Cruz';
if ($selectedProject === null) http_response_code(404);
require __DIR__ . '/includes/header.php';
?>
<main class="project-detail section-shell" id="main-content">
    <a class="collection-return" href="index.php#projects"><?= renderIcon('layout') ?> All projects</a>
    <?php if ($selectedProject === null): ?>
        <div class="detail-heading"><p class="eyebrow">WORK GALLERY</p><h1>Project not found<span class="heading-dot">.</span></h1><p>This project isn't in the collection. Choose a project from the portfolio to explore its images.</p></div>
    <?php else: ?>
        <header class="detail-overview">
            <div class="detail-heading">
                <p class="detail-project-type"><?= escapeHtml($selectedProject['projectType'] ?? ($selectedProject['category'] === 'technical' ? 'Technical Project' : 'Digital Design Project')) ?></p>
                <h1 data-project-title><?= escapeHtml($selectedProject['title']) ?><span class="heading-dot">.</span></h1>
                <p data-project-description><?= escapeHtml($selectedProject['description']) ?></p>
            </div>
            <aside class="detail-sidebar" aria-label="Project information">
                <dl class="detail-meta">
                    <div><dt class="detail-meta-label">Built with</dt><dd class="detail-technologies">
                        <?php foreach ($selectedProject['technologies'] as $technology): $technologyIconPath = getTechnologyIconPath($technology); ?>
                            <span><?php if ($technologyIconPath !== null): ?><img src="<?= escapeHtml($technologyIconPath) ?>" alt="" width="28" height="28"><?php endif; ?><?= escapeHtml($technology) ?></span>
                        <?php endforeach; ?>
                    </dd></div>
                    <div><dt class="detail-meta-label">Gallery</dt><dd class="detail-image-total"><?= count($selectedProject['images']) ?> project images</dd></div>
                </dl>
                <?php if (!empty($selectedProject['repositoryUrl']) || !empty($selectedProject['designUrl']) || !empty($selectedProject['documentUrl']) || !empty($selectedProject['videoUrl'])): ?>
                <div class="detail-resource-actions" role="group" aria-label="Project resources">
                    <?php if (!empty($selectedProject['documentUrl'])): ?><a class="button button-primary" href="<?= escapeHtml($selectedProject['documentUrl']) ?>" target="_blank" rel="noopener noreferrer"><?= renderIcon('document') ?> View Project Details (PDF)<span class="sr-only"> (opens in a new tab)</span></a><?php endif; ?>
                    <?php if (!empty($selectedProject['videoUrl'])): ?><a class="button button-secondary" href="<?= escapeHtml($selectedProject['videoUrl']) ?>" target="_blank" rel="noopener noreferrer"><?= renderIcon('play') ?> Watch Video<span class="sr-only"> (opens in a new tab)</span></a><?php endif; ?>
                    <?php if (!empty($selectedProject['repositoryUrl'])): ?><a class="button button-secondary" href="<?= escapeHtml($selectedProject['repositoryUrl']) ?>" target="_blank" rel="noopener noreferrer"><?= renderIcon('github') ?> View on GitHub<span class="sr-only"> (opens in a new tab)</span></a><?php endif; ?>
                    <?php if (!empty($selectedProject['designUrl'])): ?><a class="button button-secondary" href="<?= escapeHtml($selectedProject['designUrl']) ?>" target="_blank" rel="noopener noreferrer"><img src="assets/icons/toolkit/figma.svg" alt="" width="18" height="18"> View on Figma<span class="sr-only"> (opens in a new tab)</span></a><?php endif; ?>
                </div>
                <?php endif; ?>
            </aside>
        </header>
        <section class="detail-image-gallery" aria-labelledby="detail-gallery-title" data-carousel>
            <div class="detail-gallery-heading"><h2 id="detail-gallery-title">Inside the project</h2></div>
            <div class="detail-stage">
                <?php foreach ($selectedProject['images'] as $imageIndex => $projectImage): ?>
                    <figure class="carousel-slide detail-slide<?= $imageIndex === 0 ? ' is-active' : '' ?>" data-slide <?= $imageIndex === 0 ? '' : 'hidden' ?>>
                        <a class="detail-image-link" href="<?= escapeHtml($projectImage['src']) ?>" data-preview data-preview-title="<?= escapeHtml($selectedProject['title']) ?>" data-preview-label="<?= escapeHtml($projectImage['label']) ?>" aria-label="Enlarge <?= escapeHtml($projectImage['label']) ?>" aria-haspopup="dialog">
                            <img src="<?= escapeHtml($projectImage['src']) ?>" alt="<?= escapeHtml($projectImage['alt']) ?>" width="<?= (int) ($projectImage['width'] ?? 800) ?>" height="<?= (int) ($projectImage['height'] ?? 500) ?>" <?= $imageIndex === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                        </a>
                        <figcaption><span><?= escapeHtml($projectImage['label']) ?></span></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
            <div class="detail-screen-picker" role="group" aria-label="Choose a project image">
                <?php foreach ($selectedProject['images'] as $imageIndex => $projectImage): ?>
                    <div class="detail-screen-item">
                    <button class="detail-screen-choice<?= $imageIndex === 0 ? ' is-active' : '' ?>" type="button" data-carousel-dot data-slide-index="<?= $imageIndex ?>" aria-label="Show <?= escapeHtml($projectImage['label']) ?>" <?= $imageIndex === 0 ? 'aria-current="true"' : '' ?>>
                        <img src="<?= escapeHtml($projectImage['src']) ?>" alt="" width="180" height="105" loading="lazy">
                        <span><small><?= str_pad((string) ($imageIndex + 1), 2, '0', STR_PAD_LEFT) ?></small><?= escapeHtml($projectImage['label']) ?></span>
                    </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="detail-end"><a class="button button-secondary detail-return-button" href="index.php#projects"><?= renderIcon('arrowLeft') ?><span>Back to Gallery</span></a></div>
            <p class="sr-only" data-carousel-status role="status" aria-live="polite">Image 1 of <?= count($selectedProject['images']) ?></p>
            <noscript><ul class="detail-fallback-links"><?php foreach ($selectedProject['images'] as $projectImage): ?><li><a href="<?= escapeHtml($projectImage['src']) ?>"><?= escapeHtml($projectImage['label']) ?></a></li><?php endforeach; ?></ul></noscript>
        </section>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/imageViewer.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
