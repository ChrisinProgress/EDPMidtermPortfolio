<footer class="site-footer refresh-footer">
<div class="section-shell">
 <div class="footer-editorial">
 <div class="footer-brand"><a class="wordmark" href="<?= escapeHtml($navigationPrefix ?? '') ?>#home" aria-label="CJC home"><?= renderBrandLogo('footer') ?></a><strong class="footer-owner">Christopher John Cruz</strong><p class="mini-label">CODE. JOIN. CREATE.</p><p>Bringing practical web experiences and thoughtful visuals to life, one project at a time.</p></div>
 <nav aria-label="Footer navigation"><h3>Navigation</h3><?php foreach (['home'=>'Home','about'=>'About','projects'=>'Projects','feature'=>'Feature','contact'=>'Contact'] as $key=>$name): ?><a href="<?= escapeHtml($navigationPrefix ?? '') ?>#<?= $key ?>"><?= $name ?></a><?php endforeach; ?></nav>
 <nav aria-label="Showcase"><h3>Showcase</h3><?php foreach (['feature'=>'Recent Project','projects'=>'Work Gallery','materials'=>'Creative Materials','credentials'=>'Credentials'] as $key=>$name): ?><a href="<?= escapeHtml($navigationPrefix ?? '') ?>#<?= $key ?>"><?= $name ?></a><?php endforeach; ?></nav>
 <nav aria-label="Connect"><h3>Connect</h3><?php foreach (require __DIR__ . '/../data/socialData.php' as [$name,$url]): ?><a href="<?= escapeHtml($url) ?>" target="_blank" rel="noopener noreferrer"><?= escapeHtml($name) ?></a><?php endforeach; ?></nav>
 </div>
 <nav class="footer-policies" aria-label="Website policies"><a href="privacy.php">Privacy Policy</a><a href="terms.php">Terms of Service</a><a href="licenses.php">Licenses &amp; Attribution</a></nav>
 <div class="footer-bottom"><p>&copy; <?= date('Y') ?> Christopher John Cruz. All rights reserved.</p><a class="back-to-top" href="<?= escapeHtml($navigationPrefix ?? '') ?>#home"><span>Back to top</span><span class="back-top-bubble"><?= renderIcon('arrowUp') ?></span></a></div>
</div>
<div class="greeting-marquee" aria-label="Greetings in nine languages" tabindex="0"><div class="greeting-track"><?php for ($repeat=0;$repeat<2;$repeat++): ?><span <?= $repeat ? 'aria-hidden="true"' : '' ?>>Hello <b>•</b> Hola <b>•</b> <bdi lang="zh">你好</bdi> <b>•</b> <bdi lang="ja">おはよう</bdi> <b>•</b> Bonjour <b>•</b> <bdi lang="ko">안녕하세요</bdi> <b>•</b> <bdi lang="ar" dir="rtl">مرحبًا</bdi> <b>•</b> Kamusta <b>•</b> Ciao <b>•</b>&nbsp;</span><?php endfor; ?></div></div>
</footer>
<noscript><p class="noscript-notice">All About categories remain visible without JavaScript. Image links open their full-size files.</p></noscript>
</body></html>
