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
                <h3>Built with purpose.<br>Designed for people.</h3>
                <p>I'm Christopher John Cruz, an Information Technology student at Pamantasan ng Lungsod ng Valenzuela. I bring web development, UI/UX, and graphic design together to create useful, approachable digital experiences.</p>
                <p>Through academic and creative projects, I enjoy turning ideas into working experiences, testing the details, and improving them with a team.</p>
                <?php require __DIR__ . '/../includes/education.php'; ?>
            </div>
        </div>
        <?php require __DIR__ . '/../includes/aboutTabs.php'; ?>
    </div>
</section>
