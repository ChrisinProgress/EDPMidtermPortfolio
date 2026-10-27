<section class="credentials-section section-shell" id="credentials" data-credential-collection aria-labelledby="credentials-title">
    <div class="section-heading credentials-heading">
        <div>
            <p class="eyebrow"><span class="section-index">03</span> SEMINARS &amp; CERTIFICATES</p>
            <h2 id="credentials-title">Credentials<span class="heading-dot">.</span></h2>
        </div>
    </div>
    <div class="credentials-toolbar">
        <div class="credential-filters" role="group" aria-label="Filter credentials by issuer" data-credential-filters hidden>
            <button class="credential-filter is-active" type="button" data-credential-filter="all" aria-pressed="true">All</button>
            <button class="credential-filter" type="button" data-credential-filter="canva" aria-pressed="false">Canva</button>
            <button class="credential-filter" type="button" data-credential-filter="other" aria-pressed="false">Other issuers</button>
        </div>
        <p class="credentials-status" data-credentials-status role="status" aria-live="polite" aria-atomic="true"><?= count($credentials) ?> credentials</p>
    </div>
    <div class="credentials-grid" role="list" aria-label="Certificates and seminars">
        <?php foreach ($credentials as $credential): ?>
            <article class="credential-card" data-credential-card data-credential-issuer="<?= stripos($credential['issuer'], 'Canva') !== false ? 'canva' : 'other' ?>" role="listitem" aria-labelledby="credential-<?= escapeHtml($credential['id']) ?>">
                <a class="credential-preview" href="<?= escapeHtml($credential['image']['src']) ?>" data-preview data-preview-title="<?= escapeHtml($credential['title']) ?>" data-preview-label="Certificate" aria-label="View <?= escapeHtml($credential['title']) ?> certificate" aria-haspopup="dialog">
                    <img src="<?= escapeHtml($credential['image']['src']) ?>" alt="<?= escapeHtml($credential['image']['alt']) ?>" width="<?= $credential['image']['width'] ?>" height="<?= $credential['image']['height'] ?>" loading="lazy" decoding="async">
                    <span class="credential-enlarge" aria-hidden="true"><?= renderIcon('image') ?></span>
                </a>
                <div class="credential-content">
                    <div class="credential-meta">
                        <span class="credential-type"><?= escapeHtml($credential['type']) ?></span>
                    </div>
                    <h3 id="credential-<?= escapeHtml($credential['id']) ?>"><?= escapeHtml($credential['title']) ?></h3>
                    <p class="credential-issuer"><?= escapeHtml($credential['issuer']) ?></p>
                    <p class="credential-description"><?= escapeHtml($credential['description']) ?></p>
                    <div class="credential-footer">
                        <time datetime="<?= escapeHtml($credential['date']) ?>"><?= escapeHtml($credential['dateLabel']) ?></time>
                        <a class="credential-view" href="<?= escapeHtml($credential['image']['src']) ?>" data-open-credential aria-label="View <?= escapeHtml($credential['title']) ?> certificate" aria-haspopup="dialog">
                            <span><?= renderIcon('image') ?> View certificate</span>
                        </a>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>
