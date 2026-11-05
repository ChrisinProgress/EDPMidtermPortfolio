<?php
require_once __DIR__ . '/config/appConfig.php';
require_once __DIR__ . '/includes/icons.php';
require_once __DIR__ . '/includes/brandLogo.php';
$projects = require __DIR__ . '/data/projectData.php';
$navigationPrefix='index.php';$isLegalPage=true;$pageTitle='Policies — Christopher John Cruz';
require __DIR__ . '/includes/header.php';
?>
<main id="main-content" class="policy-page section-shell"><a class="policy-return" href="index.php"><?= renderIcon('arrowLeft') ?> Back to Portfolio</a><header class="policy-heading"><h1>Website information</h1><p>Each policy now has its own page.</p></header><nav class="policy-tabs" aria-label="Legal pages"><a id="privacy" href="privacy.php">Privacy Policy</a><a id="terms" href="terms.php">Terms of Service</a><a id="licenses" href="licenses.php">Licenses &amp; Attribution</a></nav></main>
<?php require __DIR__ . '/includes/footer.php'; ?>
