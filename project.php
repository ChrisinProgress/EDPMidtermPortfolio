<?php
declare(strict_types=1);

require_once __DIR__ . '/config/appConfig.php';
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/brandLogo.php';
startAppSession();
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
        <div class="detail-heading" data-reveal>
            <p class="eyebrow"><span class="accent-line"></span> <?= escapeHtml($selectedProject['categoryLabel']) ?> PROJECT</p>
            <h1><?= escapeHtml($selectedProject['title']) ?><span class="heading-dot">.</span></h1>
            <p><?= escapeHtml($selectedProject['description']) ?></p>
        </div>
        <div class="detail-meta" data-reveal>
            <div><span class="detail-meta-label">BUILT WITH</span><div class="detail-technologies">
                <?php foreach ($selectedProject['technologies'] as $technology): ?>
                    <?php $technologyIconPath = getTechnologyIconPath($technology); ?>
                    <span><?php if ($technologyIconPath !== null): ?><img src="<?= escapeHtml($technologyIconPath) ?>" alt="" width="24" height="24"><?php endif; ?><?= escapeHtml($technology) ?></span>
                <?php endforeach; ?>
            </div></div>
            <div><span class="detail-meta-label">THE COLLECTION</span><span class="detail-image-total"><?= count($selectedProject['images']) ?> project images</span></div>
            <?php if ($selectedProject['category'] === 'technical' && !empty($selectedProject['repositoryUrl'])): ?>
                <a class="detail-repository" href="<?= escapeHtml($selectedProject['repositoryUrl']) ?>" target="_blank" rel="noopener noreferrer"><?= renderIcon('github') ?> View on GitHub<span class="sr-only"> (opens in a new tab)</span></a>
            <?php endif; ?>
            <?php if (!empty($selectedProject['designUrl'])): ?>
                <a class="detail-repository detail-design-link" href="<?= escapeHtml($selectedProject['designUrl']) ?>" target="_blank" rel="noopener noreferrer"><img src="assets/icons/toolkit/figma.svg" alt="" width="18" height="18"> View on Figma<span class="sr-only"> (opens in a new tab)</span></a>
            <?php endif; ?>
        </div>
        <section class="detail-image-gallery" aria-labelledby="detail-gallery-title" data-carousel>
            <div class="detail-gallery-heading"><div><p class="mini-label">TAKE A CLOSER LOOK</p><h2 id="detail-gallery-title">Inside the project</h2></div><p>Select a thumbnail to explore each image.</p></div>
            <div class="detail-stage">
                <?php foreach ($selectedProject['images'] as $imageIndex => $projectImage): ?>
                    <figure class="carousel-slide detail-slide<?= $imageIndex === 0 ? ' is-active' : '' ?>" data-slide <?= $imageIndex === 0 ? '' : 'hidden' ?>>
                        <a class="detail-image-link" href="<?= escapeHtml($projectImage['src']) ?>" data-preview data-preview-title="<?= escapeHtml($selectedProject['title']) ?>" data-preview-label="<?= escapeHtml($projectImage['label']) ?>" aria-label="Enlarge <?= escapeHtml($projectImage['label']) ?>" aria-haspopup="dialog">
                            <img src="<?= escapeHtml($projectImage['src']) ?>" alt="<?= escapeHtml($projectImage['alt']) ?>" width="<?= (int) ($projectImage['width'] ?? 800) ?>" height="<?= (int) ($projectImage['height'] ?? 500) ?>" <?= $imageIndex === 0 ? 'fetchpriority="high"' : 'loading="lazy"' ?>>
                        </a>
                        <figcaption><span><?= escapeHtml($projectImage['label']) ?></span><span><?= renderIcon('image') ?> Click image to enlarge</span></figcaption>
                    </figure>
                <?php endforeach; ?>
            </div>
            <div class="detail-screen-picker" role="group" aria-label="Choose a project image">
                <?php foreach ($selectedProject['images'] as $imageIndex => $projectImage): ?>
                    <button class="detail-screen-choice<?= $imageIndex === 0 ? ' is-active' : '' ?>" type="button" data-carousel-dot data-slide-index="<?= $imageIndex ?>" aria-label="Show <?= escapeHtml($projectImage['label']) ?>" <?= $imageIndex === 0 ? 'aria-current="true"' : '' ?>>
                        <img src="<?= escapeHtml($projectImage['src']) ?>" alt="" width="180" height="105" loading="lazy">
                        <span><small><?= str_pad((string) ($imageIndex + 1), 2, '0', STR_PAD_LEFT) ?></small><?= escapeHtml($projectImage['label']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <p class="sr-only" data-carousel-status role="status" aria-live="polite">Image 1 of <?= count($selectedProject['images']) ?></p>
            <noscript><ul class="detail-fallback-links"><?php foreach ($selectedProject['images'] as $projectImage): ?><li><a href="<?= escapeHtml($projectImage['src']) ?>"><?= escapeHtml($projectImage['label']) ?></a></li><?php endforeach; ?></ul></noscript>
        </section>
        <div class="detail-end"><p>There's more to explore in my work.</p><a class="button button-secondary" href="index.php#projects">Back to Work Gallery</a></div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/includes/imageViewer.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
