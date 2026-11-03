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
    'src' => 'assets/images/profile/hero.png',
    'alt' => 'Christopher John Cruz',
];
$pixelProfileImage = 'assets/images/profile/christopher-cruz-pixel-hero-cutout.png';
$aboutProfileImage = 'assets/images/profile/campus.png';

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
        <div class="hero-visual id-scene">
 <div class="id-lanyard" aria-hidden="true"></div><div class="id-clip" aria-hidden="true"></div>
 <button class="student-id" type="button" data-student-id aria-pressed="false" aria-label="Turn Christopher's portfolio ID over">
 <span class="id-face id-front"><span class="id-heading"><span>DEVELOPER<br>&amp; DESIGNER</span><?= renderBrandLogo('portrait') ?></span>
 <span class="id-photo"><img src="<?= escapeHtml($profileImage['src']) ?>" alt="Christopher John Cruz" width="600" height="700" fetchpriority="high"><img class="id-pixel" src="assets/images/profile/christopher-cruz-pixel-hero-cutout.png" alt="" width="600" height="700" aria-hidden="true"></span>
 <strong class="id-name">Christopher John Cruz</strong><span class="id-course">BS INFORMATION TECHNOLOGY · PLV</span><span class="id-number">STUDENT NO. <strong>24-333*</strong></span><span class="id-bottom">PORTFOLIO ID </span></span>
 <span class="id-face id-back" aria-hidden="true"><?= renderBrandLogo('portrait') ?><strong>Practical code.<br>Purposeful design.</strong><span class="id-specialties"><span><?= renderIcon('code') ?> Development</span><span><?= renderIcon('palette') ?> Design</span><span><?= renderIcon('check') ?> Testing</span></span><span class="id-bottom">LET'S BUILD SOMETHING </span></span>
 </button></div>
        <div class="hero-bottom">
            <a class="scroll-cue" href="#projects"><span class="scroll-mouse" aria-hidden="true"><span></span></span><span>Scroll to explore</span><span class="scroll-cue-arrow"><?= renderIcon('arrowDown') ?></span></a>
        </div>
    </section>
    <nav class="portfolio-summary section-shell" aria-label="Portfolio at a glance"><a href="#projects"><span class="summary-icon"><?= renderIcon('code') ?></span><strong><?= count($projects) ?></strong><span>Projects<small>Systems and digital design</small></span></a><a href="#credentials"><span class="summary-icon"><?= renderIcon('award') ?></span><strong><?= count($credentials) ?></strong><span>Credentials<small>Learning put into practice</small></span></a><a href="#toolkit"><span class="summary-icon"><?= renderIcon('layers') ?></span><strong><?= count(require __DIR__ . '/data/toolkitData.php') ?></strong><span>Tech Stack<small>Tools behind my work</small></span></a></nav>
    <?php require __DIR__ . '/includes/toolkit.php'; ?>
    <?php require __DIR__ . '/pages/projects.php'; ?>
    <?php require __DIR__ . '/pages/about.php'; ?>
    <?php require __DIR__ . '/pages/credentials.php'; ?>
    <?php require __DIR__ . '/pages/contact.php'; ?>
</main>
<?php require __DIR__ . '/includes/imageViewer.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
