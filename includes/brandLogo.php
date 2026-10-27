<?php
declare(strict_types=1);

/** Render the geometric CJC monogram without repeated SVG definition IDs. */
function renderBrandLogo(string $variant = 'default'): string
{
    static $instance = 0;
    $instance++;
    $variant = preg_replace('/[^a-z0-9-]/i', '', $variant) ?: 'default';
    $source = file_get_contents(__DIR__ . '/../assets/icons/brand/cjc-monogram.svg');
    return str_replace(
        ['brandGradient', 'class="cjc-logo"'],
        ['cjc-brand-gradient-' . $instance, 'class="cjc-logo cjc-logo-' . $variant . '"'],
        $source
    );
}
