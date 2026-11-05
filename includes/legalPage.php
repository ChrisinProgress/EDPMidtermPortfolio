<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/appConfig.php';
require_once __DIR__ . '/icons.php';
require_once __DIR__ . '/brandLogo.php';
$policies = require __DIR__ . '/../data/legalData.php';
if (!isset($legalPage) || !isset($policies[$legalPage])) { http_response_code(404); exit; }
$policy = $policies[$legalPage];
$projects = require __DIR__ . '/../data/projectData.php';
$navigationPrefix = 'index.php';
$pageTitle = $policy['title'] . ' — Christopher John Cruz';
$isLegalPage = true;
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
require __DIR__ . '/header.php';
?>
<main id="main-content" class="policy-page section-shell">
    <a class="policy-return" href="index.php"><?= renderIcon('arrowLeft') ?> Back to Portfolio</a>
    <header class="policy-heading">
        <h1 data-type-heading><?= escapeHtml($policy['title']) ?><span class="heading-dot">.</span></h1>
        <p><?= escapeHtml($policy['summary']) ?></p>
        <span class="policy-updated">Last Updated <time datetime="2026-11-05">November 5, 2026</time></span>
    </header>
    <nav class="policy-tabs" aria-label="Legal pages">
        <?php foreach ($policies as $key => $entry): ?><a href="<?= escapeHtml($entry['file']) ?>" <?= $key === $legalPage ? 'aria-current="page"' : '' ?>><?= escapeHtml($entry['title']) ?></a><?php endforeach; ?>
    </nav>
    <div class="policy-layout">
        <aside class="policy-index"><nav aria-label="On this page"><h2>On this page</h2><?php foreach ($policy['sections'] as $section): ?><a href="#<?= escapeHtml($section['id']) ?>"><?= escapeHtml($section['title']) ?></a><?php endforeach; ?></nav></aside>
        <article class="policy-article" aria-label="<?= escapeHtml($policy['title']) ?> content">
            <?php foreach ($policy['sections'] as $section): ?>
            <section id="<?= escapeHtml($section['id']) ?>">
                <h2><?= escapeHtml($section['title']) ?></h2>
                <?= $section['html'] ?>
                <?php if (!empty($section['register'])): ?>
                <div class="attribution-register" role="region" tabindex="0" aria-label="Resource attribution table">
                    <table><caption>Bundled resources and verified source records</caption><thead><tr><th scope="col">Resource</th><th scope="col">Creator</th><th scope="col">Usage / Modifications</th><th scope="col">License</th><th scope="col">Source Link</th></tr></thead><tbody>
                    <?php foreach (require __DIR__ . '/../data/attributionData.php' as [$name,$creator,$usage,$license,$licenseUrl,$sourceUrl]): ?>
                    <tr><th scope="row"><?= escapeHtml($name) ?></th><td><?= escapeHtml($creator) ?></td><td><?= escapeHtml($usage) ?></td><td><?php if ($licenseUrl !== ''): ?><a href="<?= escapeHtml($licenseUrl) ?>" <?= str_starts_with($licenseUrl,'https:') ? 'target="_blank" rel="noopener noreferrer"' : '' ?>><?= escapeHtml($license) ?></a><?php else: ?><?= escapeHtml($license) ?><?php endif; ?></td><td><?php if ($sourceUrl !== ''): ?><a href="<?= escapeHtml($sourceUrl) ?>" target="_blank" rel="noopener noreferrer">Original source<span class="sr-only"> for <?= escapeHtml($name) ?> (opens in a new tab)</span></a><?php else: ?><span class="provenance-pending">Source details pending</span><?php endif; ?></td></tr>
                    <?php endforeach; ?>
                    </tbody></table>
                </div>
                <p class="policy-note">The <a href="assets/icons/toolkit/SOURCES.md">toolkit source notes</a> list individual logo files and their sources.</p>
                <?php endif; ?>
            </section>
            <?php endforeach; ?>
            <div class="policy-contact"><span><?= renderIcon('mail') ?></span><div><h2>Questions or a request?</h2><p><a href="mailto:cruchristopherjohnhoylar@gmail.com">Email Christopher John Cruz</a> with the relevant page link and details. Privacy and rights requests do not require using the public contact form.</p></div></div>
        </article>
    </div>
</main>
<?php require __DIR__ . '/footer.php'; ?>
