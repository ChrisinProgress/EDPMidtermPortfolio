<?php
declare(strict_types=1);

require_once __DIR__ . '/config/appConfig.php';
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/brandLogo.php';
require_once __DIR__ . '/includes/themePortrait.php';

startAppSession();
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

$projects = require __DIR__ . '/data/projectData.php';
$credentials = require __DIR__ . '/data/credentialsData.php';
$technicalCount = count(array_filter($projects, fn($project) => $project['category'] === 'technical'));
$designCount = count($projects) - $technicalCount;
$contactConfig = getContactConfig();
$profileImage = [
    'src' => 'assets/images/profile/christopher-cruz-hero-cutout.png',
    'alt' => 'Christopher John Cruz',
];
$pixelProfileImage = 'assets/images/profile/christopher-cruz-pixel-hero-cutout.png';
$aboutProfileImage = 'assets/images/profile/christopher-cruz-campus-portrait.png';

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="hero section-shell" id="home" aria-labelledby="home-title">
        <div class="hero-copy">
            <p class="eyebrow"><span class="accent-line"></span> IT STUDENT <span class="label-dot">&bull;</span> DEVELOPER <span class="label-dot">&bull;</span> DESIGNER</p>
            <h1 id="home-title"><span class="hero-greeting">Hi, I'm</span> Christopher<br> John <span class="name-accent">Cruz.</span></h1>
            <p class="hero-description">I'm a third-year Information Technology student at PLV who enjoys building practical web systems and creating UI/UX and graphic designs. My work brings useful features and clear visuals together to make everyday tasks easier.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#projects">View Projects <?= renderIcon('arrowUpRight') ?></a>
                <a class="button button-secondary" href="#about">About Me <?= renderIcon('arrowRight') ?></a>
            </div>
        </div>
        <div class="hero-visual hero-cutout">
            <div class="portrait-aura" aria-hidden="true"></div>
            <div class="visual-orbit orbit-one" aria-hidden="true"></div>
            <div class="visual-orbit orbit-two" aria-hidden="true"></div>
            <div class="portrait-frame">
                <div class="portrait-topline"><span>THE PERSON BEHIND THE WORK</span><?= renderBrandLogo('portrait') ?></div>
                <?= renderThemePortrait('hero', $profileImage['src'], $pixelProfileImage, 'Christopher John Cruz. Hover or activate to reveal my pixel-art portrait.', true) ?>
            </div>
            <span class="floating-icon code-icon" aria-hidden="true"><?= renderIcon('code') ?></span>
            <span class="floating-icon design-icon" aria-hidden="true"><?= renderIcon('penTool') ?></span>
            <div class="visual-sticker"><?= renderIcon('sparkles') ?><span>A little code.<br><strong>A lot of curiosity.</strong></span></div>
            <span class="hero-portrait-hint" aria-hidden="true"><span class="hint-pixels"></span> Hover or tap for my pixel side</span>
        </div>
        <div class="hero-bottom">
            <a class="scroll-cue" href="#projects"><span class="scroll-mouse" aria-hidden="true"><span></span></span><span>Scroll to explore</span><span class="scroll-cue-arrow"><?= renderIcon('arrowDown') ?></span></a>
        </div>
    </section>
    <?php require __DIR__ . '/includes/toolkit.php'; ?>
    <?php require __DIR__ . '/pages/projects.php'; ?>
    <?php require __DIR__ . '/pages/about.php'; ?>
    <?php require __DIR__ . '/pages/credentials.php'; ?>
    <?php require __DIR__ . '/pages/contact.php'; ?>
</main>
<?php require __DIR__ . '/includes/imageViewer.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
