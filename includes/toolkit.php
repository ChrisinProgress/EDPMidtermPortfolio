<?php
$toolkitItems = require __DIR__ . '/../data/toolkitData.php';
$toolkitGroups = [
    'programming' => [
        'title' => 'Programming', 'icon' => 'code',
        'intro' => 'Bringing ideas to life with code.',
        'description' => 'A growing toolkit of languages and web technologies for my coursework, interfaces, and system projects.',
    ],
    'design' => [
        'title' => 'Design', 'icon' => 'penTool',
        'intro' => 'Giving ideas a visual shape.',
        'description' => 'Tools for exploring layouts, editing images, and creating the visuals you see in my design work.',
    ],
    'databases' => [
        'title' => 'Databases', 'icon' => 'layers',
        'intro' => 'Making information make sense.',
        'description' => 'My database toolkit for organizing records, connecting tables, and practicing SQL.',
    ],
    'hosting' => [
        'title' => 'Hosting', 'icon' => 'layout',
        'intro' => 'Taking the next step: sharing my work.',
        'description' => 'Platforms for managing code, sharing projects, and exploring web deployment.',
    ],
];
?>
<section class="toolkit-section" data-toolkit-tabs aria-labelledby="toolkit-title">
    <div class="section-shell toolkit-shell">
        <div class="toolkit-heading-row">
            <h2 id="toolkit-title">My creative toolkit<span class="heading-dot">.</span></h2>
        </div>
        <div class="toolkit-tabs" role="tablist" aria-label="Choose a toolkit category">
            <?php foreach ($toolkitGroups as $toolkitGroupKey => $toolkitGroup): ?>
                <button class="toolkit-tab" id="toolkit-tab-<?= escapeHtml($toolkitGroupKey) ?>" data-toolkit-tab="<?= escapeHtml($toolkitGroupKey) ?>" type="button" role="tab" aria-controls="toolkit-panel-<?= escapeHtml($toolkitGroupKey) ?>" aria-selected="<?= $toolkitGroupKey === 'programming' ? 'true' : 'false' ?>" tabindex="<?= $toolkitGroupKey === 'programming' ? '0' : '-1' ?>"><?= escapeHtml($toolkitGroup['title']) ?></button>
            <?php endforeach; ?>
        </div>
        <div class="toolkit-groups">
            <?php foreach ($toolkitGroups as $toolkitGroupKey => $toolkitGroup): ?>
                <?php $toolkitGroupCount = count(array_filter($toolkitItems, fn($item) => $item['group'] === $toolkitGroupKey)); ?>
                <section class="toolkit-group toolkit-group-<?= escapeHtml($toolkitGroupKey) ?>" id="toolkit-panel-<?= escapeHtml($toolkitGroupKey) ?>" data-toolkit-panel="<?= escapeHtml($toolkitGroupKey) ?>" aria-labelledby="toolkit-<?= escapeHtml($toolkitGroupKey) ?>-title">
                    <div class="toolkit-group-heading">
                        <span class="toolkit-group-icon" aria-hidden="true"><?= renderIcon($toolkitGroup['icon']) ?></span>
                        <h3 id="toolkit-<?= escapeHtml($toolkitGroupKey) ?>-title"><?= escapeHtml($toolkitGroup['title']) ?></h3>
                        <span class="toolkit-group-number"><?= $toolkitGroupCount ?> tools</span>
                    </div>
                    <div class="toolkit-group-body">
                        <div class="toolkit-group-copy">
                            <p class="toolkit-group-intro"><?= escapeHtml($toolkitGroup['intro']) ?></p>
                            <p class="toolkit-group-description"><?= escapeHtml($toolkitGroup['description']) ?></p>
                        </div>
                        <ul class="toolkit-list" aria-label="<?= escapeHtml($toolkitGroup['title']) ?> tools">
                        <?php foreach ($toolkitItems as $toolkitItem): ?>
                            <?php if ($toolkitItem['group'] !== $toolkitGroupKey) { continue; } ?>
                            <li class="toolkit-item">
                                <span class="toolkit-icon-wrap">
                                    <img src="<?= escapeHtml($toolkitItem['icon']) ?>" alt="" aria-hidden="true" width="36" height="36" loading="lazy" decoding="async" draggable="false">
                                </span>
                                <span class="toolkit-item-label"><?= escapeHtml($toolkitItem['name']) ?></span>
                            </li>
                        <?php endforeach; ?>
                        </ul>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    </div>
</section>
