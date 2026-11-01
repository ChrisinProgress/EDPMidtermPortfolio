<section class="projects-section section-shell" id="projects" aria-labelledby="projects-title">
    <div class="section-heading"><div>
        <p class="eyebrow"><span class="section-index">01</span> SELECTED WORK</p>
        <h2 id="projects-title">Work Gallery<span class="heading-dot">.</span></h2>
        <p class="project-section-description">Systems, interfaces, and visual ideas I've built along the way.</p>
    </div></div>
    <div class="project-toolbar">
        <div class="project-filters" role="group" aria-label="Filter projects by category">
            <button class="filter-button is-active" type="button" data-filter="all" aria-pressed="true">ALL PROJECTS <span><?= count($projects) ?></span></button>
            <button class="filter-button" type="button" data-filter="technical" aria-pressed="false">TECHNICAL <span><?= $technicalCount ?></span></button>
            <button class="filter-button" type="button" data-filter="design" aria-pressed="false">DIGITAL DESIGN <span><?= $designCount ?></span></button>
        </div>
        <p class="results-count" data-results-count role="status" aria-live="polite"><?= count($projects) ?> projects</p>
    </div>
    <div class="work-gallery" data-work-gallery role="region" aria-label="Explore my project collection">
        <div class="work-stage" data-work-stage tabindex="0" aria-label="Project collection. Use left and right keys to browse; select the featured image to see its details.">
            <div class="gallery-neighbours">
                <?php foreach ($projects as $project): ?>
                    <button class="gallery-neighbour" type="button" data-project-neighbour="<?= escapeHtml($project['id']) ?>" aria-label="Show <?= escapeHtml($project['title']) ?>" hidden>
                        <img src="<?= escapeHtml($project['images'][0]['src']) ?>" alt="" width="600" height="360" loading="lazy">
                    </button>
                <?php endforeach; ?>
            </div>
            <div class="project-grid">
                <?php foreach ($projects as $projectIndex => $project): ?>
                    <?php require __DIR__ . '/../includes/projectCard.php'; ?>
                <?php endforeach; ?>
            </div>
        </div>
        <p class="gallery-interaction-hint">Hover or tap the featured image to reveal the project.</p>
        <div class="gallery-chooser">
            <span class="gallery-position" data-gallery-position aria-hidden="true">01 / <?= count($projects) ?></span>
            <label for="gallery-project-choice">Choose a project</label>
            <select id="gallery-project-choice" data-gallery-select>
                <?php foreach ($projects as $project): ?><option value="<?= escapeHtml($project['id']) ?>" data-option-category="<?= escapeHtml($project['category']) ?>"><?= escapeHtml($project['title']) ?></option><?php endforeach; ?>
            </select>
        </div>
        <p class="sr-only" data-gallery-status role="status" aria-live="polite" aria-atomic="true"></p>
    </div>
</section>
