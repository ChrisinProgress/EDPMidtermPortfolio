<section class="contact-section section-shell" id="contact" aria-labelledby="contact-title">
    <div class="contact-layout">
        <div class="contact-copy">
            <h2 id="contact-title" data-type-heading>Let's Work<br> <span class="contact-title-accent">Together</span><span class="heading-dot">.</span></h2>
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
        </div>
        <form class="contact-form" id="contact-form" data-native-contact action="https://formsubmit.co/cruchristopherjohnhoylar@gmail.com" method="post">
            <div class="form-heading">
                <h3>Send Me a Message</h3>
            </div>
            <input type="hidden" name="_subject" value="New portfolio inquiry"><input type="hidden" name="_template" value="table"><input type="hidden" name="_autoresponse" value="Thank you for contacting Christopher John Cruz. Your inquiry has been submitted. A copy is included below. I will review your message and reply to the email address you provided.">
            <div class="honeypot" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input id="website" type="text" name="_honey" tabindex="-1" autocomplete="off">
            </div>
            <div class="form-field"><label for="project-brief">Start with a project brief (optional)</label><select id="project-brief" name="projectBrief" data-project-brief><option value="">Write my own message</option><option value="Website or web system" data-brief="website">Website or web system</option><option value="UI/UX or graphic design" data-brief="design">UI/UX or graphic design</option><option value="Collaboration" data-brief="collaboration">Collaboration</option></select><small>Choosing a topic adds a starting point to an empty message.</small></div><div class="form-row">
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
            <label class="contact-consent"><input type="checkbox" name="privacyConsent" value="Agreed to inquiry processing" required><span>I agree to the use of my name, email, and message to respond to this inquiry, as described in the <a href="privacy.php">Privacy Policy</a>.</span></label>
            <p class="form-status" data-form-status role="status" aria-live="polite" aria-atomic="true"></p>
            <button class="button button-primary submit-button" type="submit" data-submit-button>
                <span data-submit-label>Send Message</span>
                <?= renderIcon('send') ?>
            </button>
        </form>
    </div>
</section>
