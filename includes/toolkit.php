<?php $toolkitItems = require __DIR__ . '/../data/toolkitData.php'; ?>
<section class="toolkit-section" data-toolkit-collection aria-labelledby="toolkit-title">
    <div class="section-shell toolkit-shell">
        <div class="toolkit-heading-row">
            <div>
                <p class="mini-label">THINGS I BUILD WITH</p>
                <h2 id="toolkit-title">My creative toolkit<span class="heading-dot">.</span></h2>
            </div>
            <button class="toolkit-motion-toggle" data-toolkit-motion-toggle type="button" aria-pressed="false" aria-label="Pause toolkit motion" hidden><span class="toolkit-motion-symbol" aria-hidden="true"></span><span data-toolkit-motion-label>Pause motion</span></button>
        </div>
        <p class="sr-only" id="toolkit-motion-help">All 16 tools appear in one continuous row. Hover to pause, or use Pause motion to scroll through the tools. Keyboard focus and touch also pause the row.</p>
        <div class="toolkit-ribbon" data-toolkit-row>
            <div class="toolkit-viewport" data-toolkit-viewport tabindex="0" role="region" aria-label="My tools and technologies" aria-describedby="toolkit-motion-help">
                <div class="toolkit-strip" data-toolkit-strip>
                    <ul class="toolkit-list" data-toolkit-list aria-label="Tools and technologies">
                        <?php foreach ($toolkitItems as $toolkitItem): ?>
                            <li class="toolkit-item" data-toolkit-item>
                                <span class="toolkit-icon-wrap"><img src="<?= escapeHtml($toolkitItem['icon']) ?>" alt="" aria-hidden="true" width="36" height="36" loading="lazy" decoding="async" draggable="false"></span>
                                <span class="toolkit-item-label"><?= escapeHtml($toolkitItem['name']) ?></span>
                                <span class="toolkit-item-description"><?= escapeHtml($toolkitItem['description']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</section>
