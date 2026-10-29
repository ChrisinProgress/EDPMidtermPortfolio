<?php
declare(strict_types=1);

/** Drawn letter paths stay decorative; the containing link supplies their label. */
function renderBrandLogo(string $variant = 'default'): string
{
    static $source = null;
    if ($source === null) {
        $source = file_get_contents(__DIR__ . '/../assets/icons/brand/cjc-monogram.svg');
        // The standalone favicon follows system color; inline marks follow the site theme.
        $source = preg_replace('~<style>.*?</style>~s', '', $source);
    }
    $variant = preg_replace('/[^a-z0-9-]/i', '', $variant) ?: 'default';
    return str_replace('class="cjc-logo"', 'class="cjc-logo cjc-logo-' . $variant . '"', $source);
}
