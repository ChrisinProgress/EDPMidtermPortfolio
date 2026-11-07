<?php
$contactToken ??= '';
$contactAvailable = $contactToken !== '';
$contactValues ??= [];
$contactStatus = $contactResult['message'] ?? ($contactAvailable ? '' : 'The contact form is temporarily unavailable. Please use the email link.');
$contactStatusClass = $contactStatus === '' ? '' : (!empty($contactResult['ok']) ? ' is-success' : ' is-error');
$projectBriefs = [
    ['', '', 'Write my own message'],
    ['Website or web system', 'website', 'Website or web system'],
    ['UI/UX or graphic design', 'design', 'UI/UX or graphic design'],
    ['Collaboration', 'collaboration', 'Collaboration'],
];
?>
<section class="contact-section section-shell" id="contact" aria-labelledby="contact-title">
    <div class="contact-layout">
        <div class="contact-copy">
            <h2 id="contact-title" data-type-heading>Let's Work<br> <span class="contact-title-accent">Together</span><span class="heading-dot">.</span></h2>
            <p class="contact-description">Have a project idea, collaboration opportunity, or just want to connect? Feel free to reach out. I'm always open to learning, sharing ideas, and exploring opportunities to grow as an IT student.</p>
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
        <form class="contact-form" id="contact-form" data-brevo-contact action="index.php#contact" method="post">
            <div class="form-heading">
                <h3>Send Me a Message</h3>
            </div>
            <input type="hidden" name="contactAction" value="send">
            <input type="hidden" name="contactToken" value="<?= escapeHtml($contactToken) ?>">
            <div class="honeypot" aria-hidden="true">
                <label for="website">Leave this field empty</label>
                <input id="website" type="text" name="_honey" tabindex="-1" autocomplete="off">
            </div>
            <div class="form-field">
                <label for="project-brief">Start with a project brief (optional)</label>
                <select id="project-brief" name="projectBrief" data-project-brief>
                    <?php foreach ($projectBriefs as [$value, $brief, $label]): ?>
                    <option value="<?= escapeHtml($value) ?>"<?= $brief === '' ? '' : ' data-brief="' . escapeHtml($brief) . '"' ?><?= ($contactValues['projectBrief'] ?? '') === $value ? ' selected' : '' ?>><?= escapeHtml($label) ?></option>
                    <?php endforeach; ?>
                </select>
                <small>Choosing a topic adds a starting point to an empty message.</small>
            </div>
            <div class="form-row">
                <div class="form-field">
                    <label for="contact-name">Your Name <span aria-hidden="true">*</span></label>
                    <input id="contact-name" name="name" type="text" placeholder="How should I call you?" value="<?= escapeHtml($contactValues['name'] ?? '') ?>" required maxlength="100" autocomplete="name" aria-describedby="name-error">
                    <span class="field-error" id="name-error" data-field-error="name"></span>
                </div>
                <div class="form-field">
                    <label for="contact-email">Email Address <span aria-hidden="true">*</span></label>
                    <input id="contact-email" name="email" type="email" placeholder="you@example.com" value="<?= escapeHtml($contactValues['email'] ?? '') ?>" required maxlength="254" autocomplete="email" aria-describedby="email-error">
                    <span class="field-error" id="email-error" data-field-error="email"></span>
                </div>
            </div>
            <div class="form-field">
                <label for="contact-message">Message <span aria-hidden="true">*</span></label>
                <textarea id="contact-message" name="message" placeholder="Tell me a little about your idea…" required minlength="10" maxlength="5000" rows="5" aria-describedby="message-error"><?= escapeHtml($contactValues['message'] ?? '') ?></textarea>
                <span class="field-error" id="message-error" data-field-error="message"></span>
            </div>
            <label class="contact-consent"><input type="checkbox" name="privacyConsent" value="Agreed to inquiry processing"<?= ($contactValues['privacyConsent'] ?? '') === 'Agreed to inquiry processing' ? ' checked' : '' ?> required><span>I agree to the use of my name, email, and message to respond to this inquiry, as described in the <a href="privacy.php">Privacy Policy</a>.</span></label>
            <p class="form-status<?= $contactStatusClass ?>" data-form-status role="status" aria-live="polite" aria-atomic="true"><?= escapeHtml($contactStatus) ?></p>
            <button class="button button-primary submit-button" type="submit" data-submit-button<?= $contactAvailable ? '' : ' disabled' ?>>
                <span data-submit-label>Send Message</span>
                <?= renderIcon('send') ?>
            </button>
        </form>
    </div>
</section>
