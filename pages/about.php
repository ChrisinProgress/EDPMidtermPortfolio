<section class="about-section" id="about" aria-labelledby="about-title">
    <div class="section-shell">
        <div class="section-heading">
            <div>
                <p class="eyebrow"><span class="section-index">02</span> BEYOND THE SCREEN</p>
                <h2 id="about-title">About Me<span class="heading-dot">.</span></h2>
            </div>
        </div>
        <div class="about-layout">
            <div class="about-visual">
                <?= renderThemePortrait('about', $aboutProfileImage, $aboutPixelProfileImage, 'Christopher John Cruz standing on a university campus, shown as a photograph in light mode and pixel art in dark mode.') ?>
                <div class="about-photo-caption">
                    <span class="about-monogram"><?= renderBrandLogo('about') ?></span>
                    <span>
                        <strong>Christopher John Cruz</strong>
                        <small>IT student. Developer. Designer.</small>
                    </span>
                </div>
            </div>
            <div class="about-copy">
                <p class="mini-label">WHO I AM</p>
                <h3>A little about the person<br>behind the projects.</h3>
                <p>I'm Christopher John Cruz, a 20-year-old third-year Information Technology student at Pamantasan ng Lungsod ng Valenzuela.</p>
                <p>I enjoy both the logic of programming and the freedom of visual design. My academic work brings those interests together through booking and management systems, mobile app interfaces, and brand identities. I like making screens clear and giving each project a visual character of its own.</p>
                <p>Outside my projects, I spend time gaming, watching anime, biking, and experimenting with edits. I'm still learning, and each piece I make is a chance to try an idea, solve a problem, and improve the details.</p>
                <div class="about-interest-label">WHEN I'M AWAY FROM THE KEYBOARD</div>
                <ul class="about-interests" aria-label="My interests">
                    <li>Gaming</li><li>Anime</li><li>Biking</li><li>Board games</li><li>Traveling</li><li>Editing</li>
                </ul>
            </div>
        </div>
        <?php require __DIR__ . '/../includes/education.php'; ?>
        <div class="skills-heading">
            <h3>What I Can Do</h3>
        </div>
        <div class="skills-grid">
            <?php
            $skills = [
                [
                    'title' => 'UI/UX Design',
                    'icon' => 'layout',
                    'description' => 'I design clear, easy-to-use interfaces.',
                ],
                [
                    'title' => 'Graphic Design',
                    'icon' => 'palette',
                    'description' => 'I create branding and promotional graphics.',
                ],
                [
                    'title' => 'Web Development',
                    'icon' => 'code',
                    'description' => 'I build functional, responsive websites.',
                ],
                [
                    'title' => 'System Prototyping',
                    'icon' => 'layers',
                    'description' => 'I prototype systems and interfaces.',
                ],
            ];
            foreach ($skills as $skill):
            ?>
                <article class="skill-card" tabindex="0">
                    <span class="skill-icon"><?= renderIcon($skill['icon']) ?></span>
                    <h4><?= escapeHtml($skill['title']) ?></h4>
                    <p><?= escapeHtml($skill['description']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
