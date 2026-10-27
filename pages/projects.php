<section class="projects-section section-shell" id="projects" aria-labelledby="projects-title">
    <div class="section-heading">
        <div>
            <p class="eyebrow"><span class="section-index">01</span> SELECTED WORK &amp; EXPLORATIONS</p>
            <h2 id="projects-title">Work Gallery<span class="heading-dot">.</span></h2>
            <p class="project-section-description">A collection of the systems I build and the visual ideas I bring to life.</p>
        </div>
    </div>
    <div class="project-toolbar">
        <div class="project-filters" role="group" aria-label="Filter projects by category">
            <button class="filter-button is-active" type="button" data-filter="all" aria-pressed="true">All Projects <span><?= count($projects) ?></span></button>
            <button class="filter-button" type="button" data-filter="technical" aria-pressed="false">Technical <span><?= $technicalCount ?></span></button>
            <button class="filter-button" type="button" data-filter="design" aria-pressed="false">Digital Design <span><?= $designCount ?></span></button>
        </div>
        <p class="results-count" data-results-count role="status" aria-live="polite">Showing <?= count($projects) ?> projects</p>
    </div>
    <div class="project-grid project-collection">
        <?php foreach ($projects as $projectIndex => $project): ?>
            <?php require __DIR__ . '/../includes/projectCard.php'; ?>
        <?php endforeach; ?>
    </div>
</section>
