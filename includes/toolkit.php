<?php
$toolkitItems = require __DIR__ . '/../data/toolkitData.php';
$toolkitCategories = [
    'programming' => 'Development',
    'design' => 'Design',
    'databases' => 'Databases',
    'workspace' => 'Workspace',
    'hosting' => 'Hosting',
];
$toolkitFilterLabels = ['all' => 'All'] + $toolkitCategories;
?>
<section id="toolkit" class="toolkit-section" data-toolkit-collection data-slider aria-labelledby="toolkit-title">
 <div class="section-shell toolkit-shell">
  <h2 id="toolkit-title" class="sr-only">Tools</h2>
  <div class="toolkit-filter-row" data-toolkit-filter-controls hidden>
   <label class="toolkit-sort" for="toolkit-category"><span aria-hidden="true"><?= renderIcon('layers') ?></span><span class="sr-only">Browse tools by category</span><select id="toolkit-category" aria-label="Browse tools by category" data-toolkit-select aria-controls="toolkit-tools"><?php foreach ($toolkitFilterLabels as $categoryKey=>$categoryName): ?><option value="<?= escapeHtml($categoryKey) ?>"><?= escapeHtml($categoryName === 'All' ? 'All tools' : $categoryName) ?></option><?php endforeach; ?></select></label>
   <?= renderSliderControls('tools') ?>
  </div>
  <p class="sr-only" data-toolkit-results role="status" aria-live="polite" aria-atomic="true">All tools</p>
        <div class="toolkit-ribbon" data-toolkit-row>
            <div class="toolkit-viewport" id="toolkit-tools" data-toolkit-viewport data-slider-viewport tabindex="0" role="region" aria-label="My tools and technologies" >
                <div class="toolkit-strip" data-toolkit-strip>
                    <ul class="toolkit-list" data-toolkit-list aria-label="Tools and technologies">
                        <?php foreach ($toolkitItems as $toolkitItem): ?>
                            <li class="toolkit-item" data-toolkit-item data-slide-card data-toolkit-category="<?= escapeHtml($toolkitItem['group']) ?>">
                                <span class="toolkit-icon-wrap"><img data-media-image src="<?= escapeHtml($toolkitItem['icon']) ?>?v=<?= (is_file(__DIR__ . '/../' . $toolkitItem['icon']) ? filemtime(__DIR__ . '/../' . $toolkitItem['icon']) : 0) ?>" alt="" aria-hidden="true" width="36" height="36" loading="lazy" decoding="async" draggable="false"></span>
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
