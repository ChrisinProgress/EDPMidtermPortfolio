<?php usort($credentials, static fn($a, $b) => strcmp($b['date'], $a['date'])); ?>
<section class="credentials-section section-shell" id="credentials" data-credential-collection data-slider aria-labelledby="credentials-title">
    <h2 id="credentials-title" class="sr-only">Credentials</h2>
    <div class="slider-heading"><?= renderSliderControls('certifications') ?></div>
    <div class="horizontal-gallery academy-gallery" data-slider-viewport tabindex="0" role="region" aria-label="Certifications and seminars, latest first">
        <?php foreach ($credentials as $credential): ?>
            <article class="gallery-card academy-card" data-credential-card data-slide-card aria-labelledby="credential-<?= escapeHtml($credential['id']) ?>">
                <div class="credential-panel">
                <?php $hasCertificate = !empty($credential['image']['src']) && is_file(__DIR__ . '/../' . $credential['image']['src']); if ($hasCertificate): ?>
                <?php
                $previewPath = substr($credential['image']['src'], 0, -4) . '-preview.webp';
                if (!is_file(__DIR__ . '/../' . $previewPath)) {
                    $previewPath = $credential['image']['src'];
                }
                ?>
                <div class="credential-preview">
                    <img src="<?= escapeHtml($previewPath) ?>" alt="<?= escapeHtml($credential['image']['alt']) ?>" width="<?= $credential['image']['width'] ?>" height="<?= $credential['image']['height'] ?>" loading="lazy" decoding="async">
                </div>
                <?php else: ?><?= renderMedia('', $credential['title'], 'Certificate Preview Unavailable') ?><?php endif; ?>
                <div class="credential-content">
                    <div class="credential-meta">
                        <span class="credential-type"><?= escapeHtml($credential['type']) ?></span>
                        <time datetime="<?= escapeHtml($credential['date']) ?>"><?= escapeHtml($credential['dateLabel']) ?></time>
                    </div>
                    <h3 id="credential-<?= escapeHtml($credential['id']) ?>"><?= escapeHtml($credential['title']) ?></h3>
                    <p class="credential-issuer"><?= escapeHtml($credential['issuer']) ?></p>
                    <p class="credential-description"><?= escapeHtml($credential['description']) ?></p>
                </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
