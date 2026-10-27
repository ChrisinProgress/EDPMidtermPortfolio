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
    'src' => 'assets/images/profile/profilePlaceholder.jpg',
    'alt' => 'Christopher John Cruz',
];
$pixelProfileImage = 'assets/images/profile/christopher-cruz-pixel-avatar.png';
$aboutProfileImage = 'assets/images/profile/christopher-cruz-campus-portrait.png';
$aboutPixelProfileImage = 'assets/images/profile/christopher-cruz-pixel-campus-portrait.png';

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="hero section-shell" id="home" aria-labelledby="home-title">
        <div class="hero-copy">
            <p class="eyebrow"><span class="accent-line"></span> IT STUDENT <span class="label-dot">&bull;</span> DEVELOPER <span class="label-dot">&bull;</span> DESIGNER</p>
            <h1 id="home-title"><span class="hero-greeting">Hi, I'm</span> Christopher<br> John <span class="name-accent">Cruz.</span></h1>
            <p class="hero-description">I'm a third-year Information Technology student at PLV who enjoys turning ideas into useful websites, thoughtful interfaces, and visual designs.</p>
            <p class="hero-personal-note">From <strong>system prototypes to posters</strong>, I learn by building, testing, and refining the details. This is where I share that progress.</p>
            <div class="hero-actions">
                <a class="button button-primary" href="#projects">View Projects</a>
                <a class="button button-secondary" href="#about">About Me</a>
            </div>
        </div>
        <div class="hero-visual">
            <div class="visual-orbit orbit-one" aria-hidden="true"></div>
            <div class="visual-orbit orbit-two" aria-hidden="true"></div>
            <div class="portrait-frame">
                <div class="portrait-topline"><span>THE PERSON BEHIND THE WORK</span><?= renderBrandLogo('portrait') ?></div>
                <?= renderThemePortrait('hero', $profileImage['src'], $pixelProfileImage, 'Christopher John Cruz, shown as a photograph in light mode and a pixel-art character in dark mode.', true) ?>
                <div class="portrait-mode-label" aria-hidden="true"><span class="portrait-mode-real">REAL LIFE, CREATIVE MIND</span><span class="portrait-mode-pixel">SAME ME, A PIXEL PERSPECTIVE</span></div>
            </div>
            <span class="floating-icon code-icon" aria-hidden="true"><?= renderIcon('code') ?></span>
            <span class="floating-icon design-icon" aria-hidden="true"><?= renderIcon('penTool') ?></span>
            <div class="visual-sticker"><?= renderIcon('sparkles') ?><span>A little code.<br><strong>A lot of curiosity.</strong></span></div>
        </div>
        <div class="hero-bottom">
            <a class="scroll-cue" href="#projects"><span class="scroll-icon"><?= renderIcon('arrowDown') ?></span> SCROLL TO EXPLORE</a>
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
