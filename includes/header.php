<?php $navigationPrefix = $navigationPrefix ?? ''; require_once __DIR__ . '/media.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Meet Christopher John Cruz, an Information Technology student at PLV exploring software development, UI/UX, and graphic design. Explore <?= count($projects) ?> technical and digital design projects.">
    <meta name="theme-color" content="#ffffff">
    <?php foreach ($idPortraits ?? [] as $portraitKind => $portraitPath): ?>
    <link rel="preload" as="image" href="<?= escapeHtml($portraitPath) ?>" fetchpriority="<?= $portraitKind === 'formal' ? 'high' : 'low' ?>">
    <?php endforeach; ?>
    <script src="assets/js/theme.js?v=<?= filemtime(__DIR__ . '/../assets/js/theme.js') ?>"></script>
    <script src="assets/js/pageEntrance.js?v=<?= filemtime(__DIR__ . '/../assets/js/pageEntrance.js') ?>"></script>
    <title><?= escapeHtml($pageTitle ?? 'Christopher John Cruz — Developer & Designer') ?></title>
    <link rel="icon" href="assets/icons/brand/cjc-monogram.svg?v=<?= filemtime(__DIR__ . '/../assets/icons/brand/cjc-monogram.svg') ?>" type="image/svg+xml">
    <link rel="stylesheet" href="assets/css/fonts.css">
    <link rel="stylesheet" href="assets/css/style.css?v=<?= filemtime(__DIR__ . '/../assets/css/style.css') ?>">
    <link rel="stylesheet" href="assets/css/refinements.css?v=<?= filemtime(__DIR__ . '/../assets/css/refinements.css') ?>">
    <link rel="stylesheet" href="assets/css/theme.css?v=<?= filemtime(__DIR__ . '/../assets/css/theme.css') ?>">
    <link rel="stylesheet" href="assets/css/toolkit.css?v=<?= filemtime(__DIR__ . '/../assets/css/toolkit.css') ?>">
    <link rel="stylesheet" href="assets/css/credentials.css?v=<?= filemtime(__DIR__ . '/../assets/css/credentials.css') ?>">
    <link rel="stylesheet" href="assets/css/work-gallery.css?v=<?= filemtime(__DIR__ . '/../assets/css/work-gallery.css') ?>">
    <link rel="stylesheet" href="assets/css/project-detail.css?v=<?= filemtime(__DIR__ . '/../assets/css/project-detail.css') ?>">
    <link rel="stylesheet" href="assets/css/education-socials.css?v=<?= filemtime(__DIR__ . '/../assets/css/education-socials.css') ?>">
    <link rel="stylesheet" href="assets/css/education-timeline.css?v=<?= filemtime(__DIR__ . '/../assets/css/education-timeline.css') ?>">
    <link rel="stylesheet" href="assets/css/motion.css?v=<?= filemtime(__DIR__ . '/../assets/css/motion.css') ?>">
    <link rel="stylesheet" href="assets/css/identity.css?v=<?= filemtime(__DIR__ . '/../assets/css/identity.css') ?>">
    <link rel="stylesheet" href="assets/css/creative-hero.css?v=<?= filemtime(__DIR__ . '/../assets/css/creative-hero.css') ?>">
    <link rel="stylesheet" href="assets/css/about-photos.css?v=<?= filemtime(__DIR__ . '/../assets/css/about-photos.css') ?>">
    <link rel="stylesheet" href="assets/css/page-entrance.css?v=<?= filemtime(__DIR__ . '/../assets/css/page-entrance.css') ?>">
    <link rel="stylesheet" href="assets/css/interface-polish.css?v=<?= filemtime(__DIR__ . '/../assets/css/interface-polish.css') ?>">
    <link rel="stylesheet" href="assets/css/portfolio-refresh.css?v=<?= filemtime(__DIR__ . '/../assets/css/portfolio-refresh.css') ?>">
    <script src="assets/js/portfolioRefresh.js?v=<?= filemtime(__DIR__ . '/../assets/js/portfolioRefresh.js') ?>" defer></script>
    <script src="assets/js/main.js?v=<?= filemtime(__DIR__ . '/../assets/js/main.js') ?>" defer></script>
    <script src="assets/js/portraitInteraction.js?v=<?= filemtime(__DIR__ . '/../assets/js/portraitInteraction.js') ?>" defer></script>
    <script src="assets/js/aboutPhotos.js?v=<?= filemtime(__DIR__ . '/../assets/js/aboutPhotos.js') ?>" defer></script>
    <script src="assets/js/workGallery.js?v=<?= filemtime(__DIR__ . '/../assets/js/workGallery.js') ?>" defer></script>
    <script src="assets/js/toolkitTabs.js?v=<?= filemtime(__DIR__ . '/../assets/js/toolkitTabs.js') ?>" defer></script>
    <script src="assets/js/motion.js?v=<?= filemtime(__DIR__ . '/../assets/js/motion.js') ?>" defer></script>
</head>
<body class="<?= !empty($isLegalPage) ? 'legal-page' : (!empty($isProjectDetail) ? 'project-detail-page' : 'portfolio-page') ?>">
<div class="site-entrance" data-site-entrance aria-hidden="true">
    <div class="site-entrance-inner">
    <div class="site-entrance-mark"><?= renderBrandLogo('entrance') ?></div>
    <span class="site-entrance-name">CHRISTOPHER JOHN CRUZ</span>
    <span class="site-entrance-note">Code. Join. Create.</span>
    <span class="site-entrance-line"></span><span class="entrance-percent" data-entrance-percent>0%</span>
    </div>
</div>
<div class="ambient-background" aria-hidden="true"><span class="ambient-orb ambient-orb-one"></span><span class="ambient-orb ambient-orb-two"></span><span class="ambient-orb ambient-orb-three"></span><div class="ambient-grid"></div><div class="ambient-stars"></div><svg class="ambient-waves" viewBox="0 0 1440 900" preserveAspectRatio="none"><path d="M-100 220 Q250 130 650 230 T1540 170"/><path d="M-100 580 Q350 700 770 570 T1540 650"/><path d="M-100 800 Q420 720 820 820 T1540 740"/></svg></div>
<div class="scroll-progress" data-scroll-progress aria-hidden="true"></div>
<a class="skip-link" href="#main-content">Skip to content</a>
<header class="site-header" data-site-header>
    <div class="nav-shell section-shell">
        <a class="wordmark" href="<?= escapeHtml($navigationPrefix) ?>#home" aria-label="CJC — Back to Home"><?= renderBrandLogo('header') ?></a>
        <nav class="navigation" id="mobile-nav" data-navigation aria-label="Main navigation">
            <?php foreach (['home'=>'Home','about'=>'About','projects'=>'Projects','feature'=>'Feature','contact'=>'Contact'] as $navId=>$navName): ?>
            <a href="<?= escapeHtml($navigationPrefix) ?>#<?= $navId ?>" class="nav-link" data-nav-link><?= $navName ?></a>
            <?php endforeach; ?>
        </nav>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch to dark mode" aria-pressed="false">
            <span class="theme-icon-moon"><?= renderIcon('moon') ?></span>
            <span class="theme-icon-sun"><?= renderIcon('sun') ?></span>
        </button>
        <button class="menu-toggle icon-button" type="button" data-menu-toggle aria-expanded="false" aria-controls="mobile-nav" aria-label="Open navigation menu">
            <span class="menu-glyph" aria-hidden="true"><span></span><span></span><span></span></span>
        </button>
    </div>
</header>
