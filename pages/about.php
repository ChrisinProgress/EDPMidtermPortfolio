<section class="about-section" id="about" aria-labelledby="about-title">
    <div class="section-shell">
        <div class="section-heading">
            <div>
                <h2 id="about-title" data-type-heading>About Me<span class="heading-dot">.</span></h2>
            </div>
        </div>
        <div class="about-layout">
            <div class="about-visual">
                <?php require __DIR__ . '/../includes/aboutPhotoStack.php'; ?>
                <div class="about-photo-caption">
                    <span class="about-monogram"><?= renderBrandLogo('about') ?></span>
                    <span>
                        <strong>Christopher John Cruz</strong>
                        <small>IT student. Developer. Designer.</small>
                    </span>
                </div>
            </div>
            <div class="about-copy">
                <span class="about-copy-watermark" aria-hidden="true"><?= renderBrandLogo('watermark') ?></span>
                <h3>Building Ideas.<br>Creating Experiences.</h3>
                <p>I'm Christopher John Cruz, an Information Technology student at Pamantasan ng Lungsod ng Valenzuela. I like building websites, designing UI/UX and graphics, and testing software to make sure everything looks good and works smoothly.</p>
                <p>Outside of tech, I love watching anime and movies, playing mobile games, biking, and occasionally traveling to explore new places. Whether I'm working on a project or enjoying my hobbies, I'm always up for learning something new and trying out different ideas.</p>
                <?php require __DIR__ . '/../includes/education.php'; ?>
            </div>
        </div>
        <?php require __DIR__ . '/../includes/aboutTabs.php'; ?>
    </div>
</section>
