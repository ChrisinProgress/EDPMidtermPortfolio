<?php
$aboutPhotos = [
    ['src' => $aboutProfileImage, 'label' => 'Campus', 'description' => 'Christopher John Cruz on a university campus', 'width' => 959, 'height' => 1279, 'position' => '50% 60%'],
    ['src' => 'assets/images/profile/christopher-cruz-waterfall-portrait.png', 'label' => 'Exploring', 'description' => 'Christopher John Cruz beside a waterfall', 'width' => 1536, 'height' => 2048, 'position' => '50% 62%'],
    ['src' => 'assets/images/profile/christopher-cruz-beach-portrait.png', 'label' => 'By the sea', 'description' => 'Christopher John Cruz by the sea', 'width' => 1536, 'height' => 2048, 'position' => '50% 60%'],
];
?>
<div class="about-photo-collection" data-about-photo-collection>
    <button class="about-photo-stack" type="button" data-about-photo-stack aria-label="Christopher John Cruz on a university campus. Photo 1 of 3. Show next photo." aria-describedby="about-photo-help">
        <?php foreach ($aboutPhotos as $photoIndex => $aboutPhoto): ?>
            <img class="about-stack-photo<?= $photoIndex === 0 ? ' is-current' : '' ?>" data-about-photo data-photo-label="<?= escapeHtml($aboutPhoto['label']) ?>" data-photo-description="<?= escapeHtml($aboutPhoto['description']) ?>" src="<?= escapeHtml($aboutPhoto['src']) ?>" alt="" aria-hidden="true" width="<?= (int) $aboutPhoto['width'] ?>" height="<?= (int) $aboutPhoto['height'] ?>" style="object-position: <?= escapeHtml($aboutPhoto['position']) ?>" loading="lazy" decoding="async" draggable="false">
        <?php endforeach; ?>
        <span class="about-photo-turn" aria-hidden="true"><?= renderIcon('layers') ?></span>
    </button>
    <div class="about-photo-browse">
        <span id="about-photo-help">Click or tap to turn the photo</span>
        <span class="about-photo-count" data-about-photo-status role="status" aria-live="polite" aria-atomic="true">01 / 03 <span>Campus</span></span>
    </div>
    <noscript><div class="about-photo-fallback"><?php foreach ($aboutPhotos as $aboutPhoto): ?><a href="<?= escapeHtml($aboutPhoto['src']) ?>"><?= escapeHtml($aboutPhoto['label']) ?> photo</a><?php endforeach; ?></div></noscript>
</div>
