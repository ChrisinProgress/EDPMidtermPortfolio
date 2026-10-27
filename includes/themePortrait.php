<?php
declare(strict_types=1);

/** Both supplied versions share one accessible description and a stable frame. */
function renderThemePortrait(string $context, string $realSource, string $pixelSource, string $description, bool $priority = false): string
{
    $context = preg_replace('/[^a-z0-9-]/i', '', $context) ?: 'portrait';
    $loading = $priority ? 'fetchpriority="high"' : 'loading="lazy"';
    $height = $context === 'hero' ? 480 : 640;
    return '<div class="theme-portrait theme-portrait-' . $context . '" role="img" aria-label="' . escapeHtml($description) . '">'
        . '<img class="portrait-layer portrait-real" src="' . escapeHtml($realSource) . '" alt="" aria-hidden="true" width="480" height="' . $height . '" ' . $loading . ' decoding="async">'
        . '<img class="portrait-layer portrait-pixel" src="' . escapeHtml($pixelSource) . '" alt="" aria-hidden="true" width="480" height="' . $height . '" decoding="async">'
        . '<span class="portrait-pixel-grid" aria-hidden="true"></span>'
        . '</div>';
}
