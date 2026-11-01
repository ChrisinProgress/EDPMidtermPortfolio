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
                <p class="mini-label">WHO I AM</p>
                <h3>Curious by nature.<br>Creative through code.</h3>
                <p>I'm Christopher John Cruz, a third-year Information Technology student at Pamantasan ng Lungsod ng Valenzuela. I enjoy bringing development and design together through websites, booking and management systems, mobile interfaces, and graphics.</p>
                <p>I learn by building, testing, and refining my academic projects, with a focus on clear screens and useful features. Outside my work, I recharge through gaming, anime, biking, board games, and exploring new places. Editing photos and visuals gives me another way to experiment and express ideas.</p>
                <div class="about-interest-label">WHEN I'M AWAY FROM THE KEYBOARD</div>
                <ul class="about-interests interest-grid" aria-label="My interests">
                    <?php
                    $interests = [
                        ['name' => 'Gaming', 'icon' => 'gamepad', 'note' => 'Next level'],
                        ['name' => 'Anime', 'icon' => 'anime', 'note' => 'A new world'],
                        ['name' => 'Biking', 'icon' => 'bicycle', 'note' => 'Fresh air'],
                        ['name' => 'Board games', 'icon' => 'dice', 'note' => 'One more round'],
                        ['name' => 'Traveling', 'icon' => 'compass', 'note' => 'New perspectives'],
                        ['name' => 'Editing', 'icon' => 'scissors', 'note' => 'Making the cut'],
                    ];
                    foreach ($interests as $interest):
                    ?>
                        <li class="interest-item interest-<?= escapeHtml($interest['icon']) ?>">
                            <span class="interest-icon" aria-hidden="true"><?= renderIcon($interest['icon']) ?></span>
                            <span class="interest-text"><strong><?= escapeHtml($interest['name']) ?></strong><small><?= escapeHtml($interest['note']) ?></small></span>
                        </li>
                    <?php endforeach; ?>
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
