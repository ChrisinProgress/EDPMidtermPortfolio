<section class="contact-section section-shell" id="contact" aria-labelledby="contact-title">
    <div class="contact-layout">
        <div class="contact-copy">
            <p class="eyebrow"><span class="section-index">04</span> LET'S MAKE SOMETHING MEANINGFUL</p>
            <h2 id="contact-title">Let's Work<br> <span class="contact-title-accent">Together</span><span class="heading-dot">.</span></h2>
            <p class="contact-description">Have a project idea, collaboration opportunity, or just want to connect? Feel free to reach out. I'm always open to learning, sharing ideas, and exploring opportunities to grow as an IT student and designer.</p>
            <div class="contact-links">
                <a class="contact-link email-link" href="mailto:cruchristopherjohnhoylar@gmail.com">
                    <span class="contact-link-icon"><?= renderIcon('mail') ?></span>
                    <span>
                        <small>DROP ME A LINE</small>
                        <strong>cruchristopherjohnhoylar@gmail.com</strong>
                    </span>
                    <?= renderIcon('arrowUpRight') ?>
                </a>
                <a class="contact-link" href="https://github.com/ChrisinProgress" target="_blank" rel="noopener noreferrer">
                    <span class="contact-link-icon"><?= renderIcon('github') ?></span>
                    <span>
                        <small>EXPLORE MY CODE</small>
                        <strong>GitHub <span>@ChrisinProgress</span></strong>
                    </span>
                    <?= renderIcon('arrowUpRight') ?>
                </a>
                <a class="contact-link" href="https://www.linkedin.com/in/christopherjohncruz" target="_blank" rel="noopener noreferrer">
                    <span class="contact-link-icon"><?= renderIcon('linkedin') ?></span>
                    <span>
                        <small>LET'S CONNECT</small>
                        <strong>LinkedIn</strong>
                    </span>
                    <?= renderIcon('arrowUpRight') ?>
                </a>
            </div>
            <p class="contact-signoff"><?= renderIcon('sparkles') ?> Good things start with a conversation.</p>
        </div>
        <form class="contact-form" id="contact-form" data-contact-form action="handlers/contactHandler.php" method="post">
            <div class="form-heading">
                <h3>Send Me a Message</h3>
                <p>A new idea starts with a simple hello.</p>
            </div>
            <input type="hidden" name="csrfToken" value="<?= escapeHtml(getCsrfToken()) ?>">
            <div class="honeypot" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input id="website" type="text" name="website" tabindex="-1" autocomplete="off">
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label for="contact-name">Your Name <span aria-hidden="true">*</span></label>
                    <input id="contact-name" name="name" type="text" placeholder="How should I call you?" required maxlength="100" autocomplete="name" aria-describedby="name-error">
                    <span class="field-error" id="name-error" data-field-error="name"></span>
                </div>
                <div class="form-field">
                    <label for="contact-email">Email Address <span aria-hidden="true">*</span></label>
                    <input id="contact-email" name="email" type="email" placeholder="you@example.com" required maxlength="254" autocomplete="email" aria-describedby="email-error">
                    <span class="field-error" id="email-error" data-field-error="email"></span>
                </div>
            </div>
            <div class="form-field">
                <label for="contact-message">Message <span aria-hidden="true">*</span></label>
                <textarea id="contact-message" name="message" placeholder="Tell me a little about your idea…" required minlength="10" maxlength="5000" rows="5" aria-describedby="message-error"></textarea>
                <span class="field-error" id="message-error" data-field-error="message"></span>
            </div>
            <p class="form-status" data-form-status role="status" aria-live="polite" aria-atomic="true"></p>
            <button class="button button-primary submit-button" type="submit" data-submit-button>
                <span data-submit-label>Send Message</span>
                <?= renderIcon('send') ?>
            </button>
            <p class="form-footnote">Fields marked with <span>*</span> are required.</p>
        </form>
    </div>
</section>
