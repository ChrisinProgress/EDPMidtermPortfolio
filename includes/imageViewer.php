<dialog class="lightbox" id="image-lightbox" data-lightbox aria-labelledby="lightbox-title">
    <div class="lightbox-top">
        <div><p class="eyebrow">A CLOSER LOOK</p><h2 id="lightbox-title" data-lightbox-title>Image preview</h2></div>
        <button class="icon-button lightbox-close" type="button" data-lightbox-close aria-label="Close image preview"><?= renderIcon('close') ?></button>
    </div>
    <div class="lightbox-stage"><img src="assets/images/profile/profilePlaceholder.jpg" alt="" data-lightbox-image></div>
    <div class="lightbox-bottom"><span data-lightbox-status aria-live="polite"></span></div>
    <div class="lightbox-picker" data-lightbox-picker role="group" aria-label="Choose an image"></div>
</dialog>
