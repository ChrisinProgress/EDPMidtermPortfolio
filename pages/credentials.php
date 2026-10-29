<section class="credentials-section section-shell" id="credentials" data-credential-collection aria-labelledby="credentials-title">
    <div class="section-heading credentials-heading">
        <div>
            <p class="eyebrow"><span class="section-index">03</span> SEMINARS &amp; CERTIFICATES</p>
            <h2 id="credentials-title">Credentials<span class="heading-dot">.</span></h2>
            <p class="credentials-intro">Little milestones, new skills, and more reasons to keep learning.</p>
        </div>
    </div>
    <div class="credential-gallery-heading">
        <p>My learning journey</p>
        <span class="credentials-status" data-credentials-status><?= count($credentials) ?> certifications &amp; seminars</span>
    </div>
    <div class="credential-timeline" role="list" aria-label="Certifications and seminars">
        <?php foreach ($credentials as $credentialIndex => $credential): ?>
            <article class="credential-card" style="--credential-order: <?= (int) $credentialIndex ?>" data-credential-card role="listitem" aria-labelledby="credential-<?= escapeHtml($credential['id']) ?>">
                <span class="credential-node" aria-hidden="true"><?= str_pad((string) ($credentialIndex + 1), 2, '0', STR_PAD_LEFT) ?></span>
                <a class="credential-preview" href="<?= escapeHtml($credential['image']['src']) ?>" data-preview data-preview-title="<?= escapeHtml($credential['title']) ?>" data-preview-label="Certificate" aria-label="Preview <?= escapeHtml($credential['title']) ?> certificate" aria-haspopup="dialog">
                    <img src="<?= escapeHtml($credential['image']['src']) ?>" alt="<?= escapeHtml($credential['image']['alt']) ?>" width="<?= $credential['image']['width'] ?>" height="<?= $credential['image']['height'] ?>" loading="lazy" decoding="async">
                </a>
                <div class="credential-content">
                    <div class="credential-meta">
                        <span class="credential-type"><?= escapeHtml($credential['type']) ?></span>
                        <time datetime="<?= escapeHtml($credential['date']) ?>"><?= escapeHtml($credential['dateLabel']) ?></time>
                    </div>
                    <h3 id="credential-<?= escapeHtml($credential['id']) ?>"><?= escapeHtml($credential['title']) ?></h3>
                    <p class="credential-issuer"><?= escapeHtml($credential['issuer']) ?></p>
                    <p class="credential-description"><?= escapeHtml($credential['description']) ?></p>
                    <details class="credential-details" data-credential-details>
                        <summary class="credential-view" data-credential-detail-trigger>View Details<span class="sr-only">: <?= escapeHtml($credential['title']) ?></span></summary>
                        <div class="credential-detail-content" data-credential-detail-content>
                            <dl class="credential-facts">
                                <div><dt>Issued by</dt><dd><?= escapeHtml($credential['issuer']) ?></dd></div>
                                <div><dt>Issue date</dt><dd><time datetime="<?= escapeHtml($credential['date']) ?>"><?= escapeHtml($credential['dateLabel']) ?></time></dd></div>
                                <div><dt>Credential type</dt><dd><?= escapeHtml($credential['type']) ?></dd></div>
                            </dl>
                            <p class="credential-detail-description"><?= escapeHtml($credential['description']) ?></p>
                            <a class="credential-detail-image" href="<?= escapeHtml($credential['image']['src']) ?>" target="_blank" rel="noopener" aria-label="Open full-size <?= escapeHtml($credential['title']) ?> certificate in a new tab">
                                <img src="<?= escapeHtml($credential['image']['src']) ?>" alt="<?= escapeHtml($credential['image']['alt']) ?>" width="<?= $credential['image']['width'] ?>" height="<?= $credential['image']['height'] ?>" loading="lazy" decoding="async">
                            </a>
                            <p class="credential-preview-hint">Select the certificate to open the full-size image.</p>
                        </div>
                    </details>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
    <dialog class="credential-dialog" data-credential-dialog aria-labelledby="credential-dialog-title">
        <div class="credential-dialog-heading">
            <div><p class="eyebrow">A LEARNING MILESTONE</p><h2 id="credential-dialog-title" data-credential-dialog-title>Certification details</h2></div>
            <button class="credential-dialog-close" type="button" data-credential-dialog-close aria-label="Close certification details"><?= renderIcon('close') ?></button>
        </div>
        <div data-credential-dialog-body></div>
    </dialog>
</section>
