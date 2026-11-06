<?php $artworks = require __DIR__ . '/../data/artworkData.php'; ?>
<section class="artwork-showcase" data-artwork-showcase aria-label="Graphic artwork">
 <?php foreach (array_chunk($artworks, 5) as $rowIndex=>$artworkRow): ?>
 <div class="artwork-row<?= $rowIndex ? ' artwork-row-reverse' : '' ?>" data-artwork-row>
  <div class="artwork-track" data-artwork-track><div class="artwork-sequence" data-artwork-sequence>
  <?php foreach ($artworkRow as $artwork): $artworkPath='assets/images/artwork/'.$artwork['file']; ?>
   <a class="artwork-tile" href="<?= escapeHtml($artworkPath) ?>" data-artwork aria-label="View <?= escapeHtml($artwork['alt']) ?>"><img src="<?= escapeHtml($artworkPath) ?>" alt="<?= escapeHtml($artwork['alt']) ?>" width="<?= $artwork['width'] ?>" height="<?= $artwork['height'] ?>" loading="lazy" decoding="async" draggable="false"></a>
  <?php endforeach; ?></div></div>
 </div>
 <?php endforeach; ?>
 <dialog class="artwork-dialog" data-artwork-dialog data-preview-dialog aria-label="Artwork preview"><div class="artwork-dialog-header"><button class="preview-close" type="button" data-preview-dismiss aria-label="Close artwork preview"><?= renderIcon('close') ?></button></div><img alt="" data-artwork-full></dialog>
</section>
