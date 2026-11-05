<?php $aboutData = require __DIR__ . '/../data/aboutData.php'; ?>
<div class="about-explorer" data-about-explorer>
 <div class="about-toolbar"><div class="about-tabs" role="tablist" aria-label="Explore my background" hidden data-about-tabs>
 <?php foreach (['expertise'=>'Expertise','credentials'=>'Credentials','materials'=>'Materials','toolkit'=>'Tools'] as $key=>$label): ?>
 <button type="button" role="tab" id="tab-<?= $key ?>" aria-controls="panel-<?= $key ?>" aria-selected="<?= $key === 'expertise' ? 'true' : 'false' ?>" tabindex="<?= $key === 'expertise' ? '0' : '-1' ?>" data-about-tab="<?= $key ?>"><?= $label ?></button>
 <?php endforeach; ?></div><div class="gallery-controls about-gallery-controls" data-about-slider-controls hidden><button type="button" data-about-prev aria-label="Previous items"><?= renderIcon('arrowLeft') ?></button><button type="button" data-about-next aria-label="Next items"><?= renderIcon('arrowRight') ?></button></div></div>
 <div id="panel-expertise" role="tabpanel" aria-labelledby="tab-expertise" data-about-panel="expertise">
 <section data-slider aria-label="Expertise gallery">
 <div class="slider-heading"><?= renderSliderControls('expertise cards') ?></div>
 <div class="horizontal-gallery expertise-gallery" data-slider-viewport tabindex="0" role="region" aria-label="Expertise cards">
 <?php foreach ($aboutData['expertise'] as $item): ?>
 <article class="gallery-card expertise-card" data-slide-card><div class="gallery-copy"><span class="expertise-icon"><?= renderIcon($item['icon']) ?></span><h3><?= escapeHtml($item['title']) ?></h3><p><?= escapeHtml($item['description']) ?></p></div><?= renderMedia($item['image'], $item['imageAlt'] ?? $item['title']) ?></article>
 <?php endforeach; ?></div></section></div>
 <div id="panel-credentials" role="tabpanel" aria-labelledby="tab-credentials" data-about-panel="credentials"><?php require __DIR__ . '/../pages/credentials.php'; ?></div>
 <div id="panel-materials" role="tabpanel" aria-labelledby="tab-materials" data-about-panel="materials">
 <section id="materials" data-slider aria-label="Creative materials gallery">
 <div class="slider-heading"><?= renderSliderControls('materials') ?></div>
 <div class="horizontal-gallery materials-gallery" data-slider-viewport tabindex="0" role="region" aria-label="Creative materials">
 <?php foreach ($aboutData['materials'] as $item): ?><article class="gallery-card material-card<?= !empty($item['type']) ? ' material-card--' . escapeHtml(str_replace(' ', '-', strtolower($item['type']))) : '' ?>" data-slide-card><?= renderMedia($item['image'], $item['imageAlt'] ?? $item['title'], 'Image Coming Soon', true) ?><div class="gallery-copy"><?php if (!empty($item['type'])): ?><span class="material-kind"><?= escapeHtml($item['type']) ?></span><?php endif; ?><h3><?= escapeHtml($item['title']) ?></h3><p><?= escapeHtml($item['description']) ?></p></div></article><?php endforeach; ?>
 </div></section></div>
 <div id="panel-toolkit" role="tabpanel" aria-labelledby="tab-toolkit" data-about-panel="toolkit"><?php require __DIR__ . '/toolkit.php'; ?></div>
</div>
<dialog class="material-dialog" data-material-dialog aria-label="Image preview">
 <div class="material-dialog-toolbar">
  <button type="button" class="material-preview-close" data-material-close aria-label="Close image preview"><?= renderIcon('close') ?></button>
  <button type="button" class="material-preview-zoom" data-material-zoom aria-label="Zoom in" aria-pressed="false" hidden><?= renderIcon('zoom') ?></button>
 </div>
 <div class="material-image-stage"><img alt="" data-material-full></div>
</dialog>
