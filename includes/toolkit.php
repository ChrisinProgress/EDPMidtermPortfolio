<?php
$toolkitItems = require __DIR__ . '/../data/toolkitData.php';
$toolkitCategories = [
    'programming' => 'Development',
    'design' => 'Design',
    'databases' => 'Databases',
    'hosting' => 'Hosting',
];
$toolkitFilterCounts = array_count_values(array_column($toolkitItems, 'group'));
$toolkitFilterLabels = ['all' => 'All'] + $toolkitCategories;
?>
<section class="toolkit-section" data-toolkit-collection aria-labelledby="toolkit-title">
    <div class="section-shell toolkit-shell">
        <div class="toolkit-heading-row">
            <div>
                <p class="mini-label">THINGS I BUILD WITH</p>
                <h2 id="toolkit-title">My creative toolkit<span class="heading-dot">.</span></h2>
            </div>
            <button class="toolkit-motion-toggle" data-toolkit-motion-toggle type="button" aria-pressed="false" aria-label="Pause toolkit motion" hidden><span class="toolkit-motion-symbol" aria-hidden="true"></span><span data-toolkit-motion-label>Pause motion</span></button>
        </div>
        <div class="toolkit-filter-row" data-toolkit-filter-controls hidden>
            <div class="toolkit-filters" role="group" aria-label="Filter tools by category">
                <?php foreach ($toolkitFilterLabels as $categoryKey => $categoryName): ?>
                    <button class="toolkit-filter<?= $categoryKey === 'all' ? ' is-active' : '' ?>" type="button" data-toolkit-filter="<?= escapeHtml($categoryKey) ?>" data-toolkit-label="<?= escapeHtml($categoryName) ?>" aria-pressed="<?= $categoryKey === 'all' ? 'true' : 'false' ?>" aria-controls="toolkit-tools">
                        <span class="toolkit-filter-symbol" aria-hidden="true">
                            <?php if ($categoryKey === 'databases'): ?>
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><ellipse cx="12" cy="5" rx="8" ry="3"/><path d="M4 5v14c0 4 16 4 16 0V5M4 12c0 4 16 4 16 0"/></svg>
                            <?php elseif ($categoryKey === 'hosting'): ?>
                                <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M7 18a5 5 0 1 1 .6-10A6.2 6.2 0 0 1 20 10a4 4 0 0 1-1 8M12 21V11m-3 3 3-3 3 3"/></svg>
                            <?php else: ?>
                                <?= renderIcon(['all' => 'layout', 'programming' => 'code', 'design' => 'palette'][$categoryKey]) ?>
                            <?php endif; ?>
                        </span>
                        <span class="toolkit-filter-name"><?= escapeHtml($categoryName) ?></span>
                        <span class="toolkit-filter-count"><?= $categoryKey === 'all' ? count($toolkitItems) : ($toolkitFilterCounts[$categoryKey] ?? 0) ?><span class="sr-only"> tools</span></span>
                    </button>
                <?php endforeach; ?>
            </div>
            <p class="toolkit-results" data-toolkit-results role="status" aria-live="polite" aria-atomic="true">All <?= count($toolkitItems) ?> tools</p>
        </div>
        <p class="sr-only" id="toolkit-motion-help">All tools appear in one moving row. Hover to pause, or use Pause motion to scroll through the tools. Keyboard focus and touch also pause the row. Choose a category for a still, easy-to-browse grid.</p>
        <div class="toolkit-ribbon" data-toolkit-row>
            <div class="toolkit-viewport" id="toolkit-tools" data-toolkit-viewport tabindex="0" role="region" aria-label="My tools and technologies" aria-describedby="toolkit-motion-help">
                <div class="toolkit-strip" data-toolkit-strip>
                    <ul class="toolkit-list" data-toolkit-list aria-label="Tools and technologies">
                        <?php foreach ($toolkitItems as $toolkitItem): ?>
                            <li class="toolkit-item" data-toolkit-item data-toolkit-category="<?= escapeHtml($toolkitItem['group']) ?>">
                                <span class="toolkit-icon-wrap"><img src="<?= escapeHtml($toolkitItem['icon']) ?>?v=<?= filemtime(__DIR__ . '/../' . $toolkitItem['icon']) ?>" alt="" aria-hidden="true" width="36" height="36" loading="lazy" decoding="async" draggable="false"></span>
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
