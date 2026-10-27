<?php
declare(strict_types=1);

// Run from any directory: php tools/verifyPortfolio.php. No session or mail is started.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$rootPath = dirname(__DIR__);
require_once $rootPath . '/config/appConfig.php';
$projects = require $rootPath . '/data/projectData.php';
$credentials = require $rootPath . '/data/credentialsData.php';
$failureMessages = [];
$checkCount = 0;
$check = function (bool $condition, string $description) use (&$failureMessages, &$checkCount): void {
    $checkCount++;
    if (!$condition) {
        $failureMessages[] = $description;
    }
};

$check(is_array($projects) && count($projects) === 11, 'There must be exactly 11 projects.');
if (!is_array($projects)) {
    fwrite(STDERR, implode(PHP_EOL, $failureMessages) . PHP_EOL);
    exit(1);
}

$categoryCounts = array_count_values(array_column($projects, 'category'));
$check(($categoryCounts['technical'] ?? 0) === 6, 'There must be 6 technical projects.');
$check(($categoryCounts['design'] ?? 0) === 5, 'There must be 5 digital design projects.');
$projectIds = [];
$imagePaths = [];
$imageHashes = [];
$placeholderCount = 0;
$screenshotCount = 0;

foreach ($projects as $projectIndex => $project) {
    $projectLabel = (string) ($project['title'] ?? 'Project ' . ($projectIndex + 1));
    foreach (['id', 'title', 'description', 'categoryLabel'] as $fieldName) {
        $check(isset($project[$fieldName]) && is_string($project[$fieldName]) && trim($project[$fieldName]) !== '', $projectLabel . ' needs ' . $fieldName . '.');
    }
    $projectIds[] = $project['id'] ?? '';
    $check(in_array($project['category'] ?? '', ['technical', 'design'], true), $projectLabel . ' has an unsupported category.');
    $technologies = $project['technologies'] ?? [];
    $check(is_array($technologies) && $technologies !== [], $projectLabel . ' needs technologies.');
    foreach (is_array($technologies) ? $technologies : [] as $technology) {
        $check(is_string($technology) && trim($technology) !== '', $projectLabel . ' has an empty technology.');
    }
    $images = $project['images'] ?? [];
    $expectedImageCount = [
        'recipe-management-system' => 5,
        'group-portfolio' => 5,
        'dental-clinic-management-system' => 8,
        'dental-clinic-booking-system' => 6,
        'savemore-digital-bank' => 6,
        'artist-profile' => 3,
        'cozy-grind-brand-identity' => 3,
        'social-media-advertisement' => 3,
    ][$project['id'] ?? ''] ?? 4;
    $check(is_array($images) && count($images) === $expectedImageCount, $projectLabel . ' needs ' . $expectedImageCount . ' gallery images.');
    $projectImagePaths = [];
    foreach (is_array($images) ? $images : [] as $image) {
        $imagePath = (string) ($image['src'] ?? '');
        $projectImagePaths[] = $imagePath;
        $imagePaths[] = $imagePath;
        $check($imagePath !== '' && is_file($rootPath . '/' . $imagePath), $projectLabel . ': missing image ' . $imagePath);
        $check(trim((string) ($image['label'] ?? '')) !== '', $projectLabel . ': missing image label.');
        $imageAlt = trim((string) ($image['alt'] ?? ''));
        $check($imageAlt !== '', $projectLabel . ': missing image alternative text.');
        $isPlaceholder = $image['isPlaceholder'] ?? true;
        $check(is_bool($isPlaceholder), $projectLabel . ': isPlaceholder must be a boolean.');
        if ($isPlaceholder) {
            $placeholderCount++;
            $check(stripos($imageAlt, 'placeholder') !== false, $projectLabel . ': alt text must disclose the placeholder.');
        } else {
            $screenshotCount++;
        }
        if (is_file($rootPath . '/' . $imagePath)) {
            $imageSource = file_get_contents($rootPath . '/' . $imagePath);
            if ($isPlaceholder) {
                $check(stripos($imageSource, 'placeholder') !== false, $imagePath . ' must be labeled as a placeholder.');
            } else {
                $imageInfo = @getimagesize($rootPath . '/' . $imagePath);
                $check($imageInfo !== false && $imageInfo[0] > 0 && $imageInfo[1] > 0, $imagePath . ' must be a valid raster screenshot.');
            }
            $imageHashes[] = hash('sha256', $imageSource);
        }
    }
    $check(count(array_unique($projectImagePaths)) === $expectedImageCount, $projectLabel . ' must use ' . $expectedImageCount . ' different image paths.');
}

$check(count(array_unique($projectIds)) === 11, 'Project IDs must be unique.');
$check(count($imagePaths) === 51 && count(array_unique($imagePaths)) === 51, 'All 51 gallery image paths must be distinct.');
$check(count($imageHashes) === 51 && count(array_unique($imageHashes)) === 51, 'All 51 gallery images must have distinct contents.');

// Credentials are separate from the project galleries and their image totals.
$check(is_array($credentials) && count($credentials) === 8, 'There must be eight credentials.');
$credentialIds = [];
$credentialImagePaths = [];
foreach (is_array($credentials) ? $credentials : [] as $credentialIndex => $credential) {
    $credentialLabel = (string) ($credential['title'] ?? 'Credential ' . ($credentialIndex + 1));
    foreach (['id', 'title', 'type', 'issuer', 'date', 'dateLabel', 'description'] as $fieldName) {
        $check(isset($credential[$fieldName]) && is_string($credential[$fieldName]) && trim($credential[$fieldName]) !== '', $credentialLabel . ' needs ' . $fieldName . '.');
    }
    $credentialIds[] = $credential['id'] ?? '';
    $dateValue = (string) ($credential['date'] ?? '');
    $credentialDate = DateTimeImmutable::createFromFormat('!Y-m-d', $dateValue);
    $check($credentialDate !== false && $credentialDate->format('Y-m-d') === $dateValue, $credentialLabel . ' needs a valid ISO date.');
    $credentialImage = $credential['image'] ?? [];
    $credentialImagePath = (string) ($credentialImage['src'] ?? '');
    $credentialImagePaths[] = $credentialImagePath;
    $check($credentialImagePath !== '' && is_file($rootPath . '/' . $credentialImagePath), $credentialLabel . ': missing certificate image ' . $credentialImagePath);
    $check(trim((string) ($credentialImage['alt'] ?? '')) !== '', $credentialLabel . ': missing certificate alternative text.');
    if ($credentialImagePath !== '' && is_file($rootPath . '/' . $credentialImagePath)) {
        $credentialImageInfo = @getimagesize($rootPath . '/' . $credentialImagePath);
        $check($credentialImageInfo !== false && $credentialImageInfo[0] > 0 && $credentialImageInfo[1] > 0, $credentialLabel . ': certificate must be a valid raster image.');
        $check($credentialImageInfo !== false && ($credentialImage['width'] ?? null) === $credentialImageInfo[0] && ($credentialImage['height'] ?? null) === $credentialImageInfo[1], $credentialLabel . ': image dimensions must match the certificate file.');
    }
}
$check(count(array_unique($credentialIds)) === count($credentialIds), 'Credential IDs must be unique.');
$check(count(array_intersect($projectIds, $credentialIds)) === 0, 'Credential and project IDs must be different.');
$check(count(array_unique($credentialImagePaths)) === count($credentialImagePaths), 'Credentials must use different certificate images.');

// Check literal template imports and browser asset references without executing the page.
$sourcePaths = [$rootPath . '/index.php', $rootPath . '/project.php'];
foreach (['includes', 'pages', 'config', 'handlers', 'assets/css', 'assets/js'] as $sourceDirectory) {
    foreach (glob($rootPath . '/' . $sourceDirectory . '/*') as $sourcePath) {
        if (is_file($sourcePath)) {
            $sourcePaths[] = $sourcePath;
        }
    }
}
$htmlIds = [];
$fragmentReferences = [];
foreach ($sourcePaths as $sourcePath) {
    $source = file_get_contents($sourcePath);
    preg_match_all('~\bid\s*=\s*([\'"])([a-zA-Z][a-zA-Z0-9_-]*)\1~', $source, $idMatches, PREG_SET_ORDER);
    foreach ($idMatches as $idMatch) {
        $htmlIds[] = $idMatch[2];
    }
    $referenceBasePath = basename(dirname($sourcePath)) === 'handlers' ? dirname($sourcePath) : $rootPath;
    preg_match_all('~\b(?:require_once|require|include_once|include)\s+__DIR__\s*\.\s*([\'"])([^\'"]+)\1~', $source, $imports, PREG_SET_ORDER);
    foreach ($imports as $import) {
        $check(is_file(dirname($sourcePath) . $import[2]), basename($sourcePath) . ': missing PHP include ' . $import[2]);
    }
    preg_match_all('~\b(?:src|href|action)\s*=\s*([\'"])([^\'"]+)\1~i', $source, $references, PREG_SET_ORDER);
    foreach ($references as $reference) {
        $referencePath = html_entity_decode($reference[2], ENT_QUOTES, 'UTF-8');
        if (str_starts_with($referencePath, '#') && strlen($referencePath) > 1) {
            $fragmentReferences[] = [basename($sourcePath), substr($referencePath, 1)];
        }
        if (preg_match('~^(?:[a-z][a-z0-9+.-]*:|//|#)~i', $referencePath) || str_contains($referencePath, '<')) {
            continue;
        }
        $referencePath = preg_split('/[?#]/', $referencePath, 2)[0];
        $check(is_file($referenceBasePath . '/' . $referencePath), basename($sourcePath) . ': missing local reference ' . $referencePath);
    }
    preg_match_all('~(?<![a-zA-Z0-9_])url\(\s*[\'"]?([^\)\'"\s]+)~i', $source, $cssReferences);
    foreach ($cssReferences[1] as $referencePath) {
        if (!preg_match('~^(?:[a-z][a-z0-9+.-]*:|//|#)~i', $referencePath)) {
            $check(is_file(dirname($sourcePath) . '/' . $referencePath), basename($sourcePath) . ': missing CSS asset ' . $referencePath);
        }
    }
}
foreach ($fragmentReferences as [$sourceName, $fragmentId]) {
    $check(in_array($fragmentId, $htmlIds, true), $sourceName . ': missing section or element for #' . $fragmentId);
}

$svgCount = 0;
$svgHashes = [];
$hasXmlSupport = function_exists('simplexml_load_file');
$previousXmlSetting = $hasXmlSupport ? libxml_use_internal_errors(true) : false;
$assetFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($rootPath . '/assets', FilesystemIterator::SKIP_DOTS));
foreach ($assetFiles as $assetFile) {
    if ($assetFile->isFile() && strtolower($assetFile->getExtension()) === 'svg') {
        $svgCount++;
        $svgHashes[] = hash_file('sha256', $assetFile->getPathname());
        if ($hasXmlSupport) {
            $svg = simplexml_load_file($assetFile->getPathname(), 'SimpleXMLElement', LIBXML_NONET);
            $check($svg !== false && $svg->getName() === 'svg', 'Invalid SVG XML: ' . $assetFile->getFilename());
            libxml_clear_errors();
        }
    }
}
if ($hasXmlSupport) {
    libxml_use_internal_errors($previousXmlSetting);
}
$check($svgCount > 0 && count(array_unique($svgHashes)) === $svgCount, 'Every SVG asset must have distinct contents.');

if ($failureMessages !== []) {
    fwrite(STDERR, 'Portfolio verification failed:' . PHP_EOL . '- ' . implode(PHP_EOL . '- ', $failureMessages) . PHP_EOL);
    exit(1);
}

echo 'PASS: ' . $checkCount . ' checks; 11 projects (6 technical, 5 design), 51 distinct gallery images (' . $screenshotCount . ' screenshots, ' . $placeholderCount . ' labeled placeholders), ' . count($credentials) . ' credentials, and local references.' . PHP_EOL;
echo $hasXmlSupport ? 'Validated ' . $svgCount . ' distinct SVG files as XML.' . PHP_EOL : 'SVG XML validation skipped: SimpleXML is unavailable.' . PHP_EOL;
