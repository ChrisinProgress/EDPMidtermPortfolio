<?php
/** Shared media slot. Existing local images remain usable without JavaScript. */
function renderMedia(string $src, string $alt, string $label = 'Add Image', bool $preview = false): string {
    $available = $src !== '' && is_file(__DIR__ . '/../' . $src);
    $placeholder = '<span class="media-placeholder"><span aria-hidden="true">' . renderIcon('image') . '</span><span>' . escapeHtml($label) . '</span></span>';
    if (!$available) return '<div class="media-slot is-placeholder">' . $placeholder . '</div>';
    $image = '<img data-media-image src="' . escapeHtml($src) . '" alt="' . escapeHtml($alt) . '" width="800" height="500" loading="lazy" decoding="async">';
    $body = $image . '<span class="media-fallback" hidden>' . $placeholder . '</span>';
    return $preview ? '<a class="media-slot" href="' . escapeHtml($src) . '" data-media-preview aria-label="View ' . escapeHtml($alt) . '">' . $body . '</a>' : '<div class="media-slot">' . $body . '</div>';
}

function renderSliderControls(string $label): string {
    return '<div class="gallery-controls" data-slider-controls hidden><button type="button" data-slide-prev aria-label="Previous ' . escapeHtml($label) . '">' . renderIcon('arrowLeft') . '</button><button type="button" data-slide-next aria-label="Next ' . escapeHtml($label) . '">' . renderIcon('arrowRight') . '</button></div>';
}
