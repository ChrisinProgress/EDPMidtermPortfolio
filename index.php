<?php
declare(strict_types=1);

require_once __DIR__ . '/config/appConfig.php';
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/brandLogo.php';
require_once __DIR__ . '/includes/media.php';

header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');

$contactResult = null;
$contactValues = [];
$contactToken = '';
$contactPost = ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && ($_POST['contactAction'] ?? null) === 'send';
if ($contactPost || contactUsesBrevo()) {
    require_once __DIR__ . '/includes/contactDelivery.php';
    if ($contactPost) {
        [$status, $contactResult, $contactValues] = submitPortfolioContact($_POST);
        http_response_code($status);
        header('Cache-Control: no-store');
        if (stripos($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json') !== false) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($contactResult, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR);
            exit;
        }
        $contactToken = $contactResult['nextToken'] ?? (is_string($_POST['contactToken'] ?? null) ? $_POST['contactToken'] : '');
    } else {
        $contactToken = contactFormToken();
    }
}

$projects = require __DIR__ . '/data/projectData.php';
$credentials = require __DIR__ . '/data/credentialsData.php';
$technicalCount = count(array_filter($projects, fn($project) => $project['category'] === 'technical'));
$designCount = count($projects) - $technicalCount;
$idPortraits = [
    'formal' => 'assets/images/profile/hero.png',
    'formal-pixel' => 'assets/images/profile/christopher-cruz-pixel-hero-cutout.png',
    'cloak' => 'assets/images/profile/akatsuki-portrait.png',
    'cloak-pixel' => 'assets/images/profile/akatsuki-pixel.png',
];

require __DIR__ . '/includes/header.php';
?>
<main id="main-content">
    <section class="hero section-shell" id="home" aria-labelledby="home-title">
        <div class="hero-copy">
            <div class="hero-social-slot" data-social-slot><nav class="hero-socials" data-social-dock aria-label="Social media"><?php foreach (require __DIR__ . '/data/socialData.php' as [$socialName, $socialUrl, $socialIcon]): ?><a href="<?= escapeHtml($socialUrl) ?>" aria-label="<?= escapeHtml($socialName) ?>" title="<?= escapeHtml($socialName) ?>" target="_blank" rel="noopener noreferrer"><?= renderIcon($socialIcon) ?></a><?php endforeach; ?></nav></div>
            <h1 id="home-title" aria-label="Hi, I'm Christopher John Cruz."><span class="hero-greeting" aria-hidden="true">Hi, I'm</span><span class="hero-name-line" aria-hidden="true"><span data-typing-part>Christopher</span></span><span class="hero-name-line" aria-hidden="true"><span data-typing-part>John </span><span class="name-accent" data-typing-part>Cruz.</span><span class="typing-cursor"></span></span></h1>
            <p class="hero-description"><span data-hero-sentence>Your friendly 3rd yr IT student who loves bringing ideas to life.</span> <span data-hero-sentence>I enjoy building websites, designing UI/UX and graphics, and exploring software testing.</span></p>
            <div class="hero-actions">
                <a class="button button-primary" href="#feature">View Recent Project <?= renderIcon('arrowUpRight') ?></a>
                <a class="button button-secondary" href="#about">About Me <?= renderIcon('arrowRight') ?></a>
            </div>
        </div>
        <?php require __DIR__ . '/includes/portfolioId.php'; ?>
        <div class="hero-bottom">
            <a class="scroll-cue" href="#projects"><span class="scroll-mouse" aria-hidden="true"><span></span></span><span>Scroll to explore</span><span class="scroll-cue-arrow"><?= renderIcon('arrowDown') ?></span></a>
        </div>
    </section>
    <nav class="portfolio-summary section-shell" aria-label="Portfolio at a glance"><a href="#projects"><span class="summary-icon"><?= renderIcon('code') ?></span><strong><?= count($projects) ?></strong><span>Work Gallery<small>Systems and digital design</small></span></a><a href="#credentials"><span class="summary-icon"><?= renderIcon('award') ?></span><strong><?= count($credentials) ?></strong><span>Credentials<small>Learning put into practice</small></span></a><a href="#toolkit"><span class="summary-icon"><?= renderIcon('layers') ?></span><strong><?= count(require __DIR__ . '/data/toolkitData.php') ?></strong><span>Tools<small>Tools behind my work</small></span></a></nav>
    <?php require __DIR__ . '/pages/about.php'; ?>
    <?php require __DIR__ . '/pages/projects.php'; ?>
    <?php require __DIR__ . '/pages/artwork.php'; ?>
    <?php require __DIR__ . '/pages/feature.php'; ?>
    <?php require __DIR__ . '/pages/contact.php'; ?>
</main>
<?php require __DIR__ . '/includes/imageViewer.php'; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
