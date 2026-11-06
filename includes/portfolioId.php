<div class="hero-visual id-scene" data-id-scene>
    <div class="id-lanyard" data-id-lanyard aria-hidden="true">
        <svg class="id-lace-clouds" viewBox="0 0 36 64" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path id="id-cloud" d="M5 16c4 2 7 1 9-2a5 5 0 0 1 9 0c7-3 11 6 5 9H14c-5 0-8-3-9-7Z" fill="#b91c1c" stroke="#fff" stroke-width="1.25" stroke-linejoin="round"/>
            <use href="#id-cloud" x="0" y="26"/>
        </svg>
    </div>
    <div class="id-motion" data-id-motion>
        <div class="id-clip" data-id-clip aria-hidden="true"></div>
        <div class="student-id" id="portfolio-id" data-student-id>
            <div class="id-face id-front" data-id-front>
                <div class="id-heading"><span>DEVELOPER<br>&amp; DESIGNER</span><?= renderBrandLogo('portrait') ?></div>
                <button class="id-photo" type="button" data-id-photo aria-label="Toggle Christopher John Cruz’s pixel portrait" aria-pressed="false">
                    <?php foreach ($idPortraits as $kind => $path): ?>
                    <img class="id-portrait id-portrait--<?= escapeHtml($kind) ?>" src="<?= escapeHtml($path) ?>" alt="" width="1254" height="1254" draggable="false" decoding="async" aria-hidden="true">
                    <?php endforeach; ?>
                </button>
                <strong class="id-name">Christopher John Cruz</strong>
                <span class="id-course">BS INFORMATION TECHNOLOGY · PLV</span>
                <span class="id-number">STUDENT NO. <strong>24-33**</strong></span>
                <span class="id-bottom">PORTFOLIO ID</span>
            </div>
            <div class="id-face id-back" data-id-back aria-hidden="true" inert>
                <?= renderBrandLogo('portrait') ?>
                <strong>Practical code.<br>Purposeful design.</strong>
                <span class="id-specialties">
                    <span><?= renderIcon('code') ?> Development</span>
                    <span><?= renderIcon('palette') ?> Design</span>
                    <span><?= renderIcon('check') ?> Testing</span>
                </span>
                <span class="id-bottom">LET'S BUILD SOMETHING</span>
            </div>
        </div>
        <button class="id-flip" type="button" data-id-flip aria-controls="portfolio-id" aria-pressed="false" aria-label="Flip portfolio ID">
            <?= renderIcon('layers') ?>
        </button>
    </div>
</div>
