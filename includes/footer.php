<footer class="site-footer">
    <div class="section-shell">
        <div class="footer-top" data-reveal>
            <div class="footer-brand">
                <a class="wordmark" href="<?= escapeHtml($navigationPrefix ?? '') ?>#home" aria-label="CJC — Back to Home"><?= renderBrandLogo('footer') ?></a>
                <p>Thoughtful design. Useful code.<br>Always learning.</p>
            </div>
            <div class="footer-connect">
                <p class="footer-connect-label">LET'S STAY CONNECTED</p>
                <nav class="footer-socials" aria-label="Social media and contact">
                    <a class="footer-social-link" href="https://web.facebook.com/cj.cruz.9484" target="_blank" rel="noopener noreferrer" title="Facebook" aria-label="Christopher John Cruz on Facebook (opens in a new tab)">
                        <svg class="icon" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M13.5 21v-8h2.8l.4-3h-3.2V8.1c0-.9.3-1.6 1.6-1.6h1.7V3.8a22 22 0 0 0-2.5-.1c-2.5 0-4.1 1.5-4.1 4.2V10H7.5v3h2.7v8h3.3Z"/></svg>
                        <span class="sr-only">Facebook</span>
                    </a>
                    <a class="footer-social-link" href="https://www.instagram.com/jhncrzs/" target="_blank" rel="noopener noreferrer" title="Instagram" aria-label="jhncrzs on Instagram (opens in a new tab)">
                        <svg class="icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                        <span class="sr-only">Instagram</span>
                    </a>
                    <a class="footer-social-link" href="https://github.com/ChrisinProgress" target="_blank" rel="noopener noreferrer" title="GitHub" aria-label="ChrisinProgress on GitHub (opens in a new tab)">
                        <?= renderIcon('github') ?><span class="sr-only">GitHub</span>
                    </a>
                    <a class="footer-social-link" href="https://www.linkedin.com/in/christopherjohncruz" target="_blank" rel="noopener noreferrer" title="LinkedIn" aria-label="Christopher John Cruz on LinkedIn (opens in a new tab)">
                        <?= renderIcon('linkedin') ?><span class="sr-only">LinkedIn</span>
                    </a>
                    <a class="footer-social-link" href="mailto:cruchristopherjohnhoylar@gmail.com" title="Email" aria-label="Email Christopher John Cruz">
                        <?= renderIcon('mail') ?><span class="sr-only">Email</span>
                    </a>
                </nav>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026 Christopher John Cruz. All rights reserved.</p>
            <a class="back-to-top" href="<?= escapeHtml($navigationPrefix ?? '') ?>#home">Back to top <?= renderIcon('arrowUp') ?></a>
        </div>
    </div>
</footer>
<noscript>
    <p class="noscript-notice">JavaScript enables project filters, gallery controls, and image previews. You can still browse every project, open its first image, follow links, and submit the contact form.</p>
</noscript>
</body>
</html>
