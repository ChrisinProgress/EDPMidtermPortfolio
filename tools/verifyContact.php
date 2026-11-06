<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// Optional local URL checks the response headers as well as the rendered form.
$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
        exit(1);
    }
};

if (isset($argv[1])) {
    $url = $argv[1];
    $parts = parse_url($url);
    $check(is_array($parts) && in_array($parts['scheme'] ?? '', ['http', 'https'], true)
        && in_array($parts['host'] ?? '', ['localhost', '127.0.0.1', '[::1]'], true),
        'Use a local preview URL.');
    $html = file_get_contents($url, false, stream_context_create(['http' => ['follow_location' => 0, 'timeout' => 5]]));
    $check(is_string($html), 'Local preview did not return a page.');
    $check(preg_grep('/^Set-Cookie:/i', $http_response_header ?? []) === [], 'Public home set a cookie.');
} else {
    ob_start();
    require dirname(__DIR__) . '/index.php';
    $html = ob_get_clean();
}

$previous = libxml_use_internal_errors(true);
$document = new DOMDocument();
$document->loadHTML('<?xml encoding="UTF-8">' . $html);
libxml_clear_errors();
libxml_use_internal_errors($previous);
$xpath = new DOMXPath($document);
$form = '//form[@id="contact-form"]';
$check($xpath->query($form . '[@method="post" and @data-native-contact]')->length === 1, 'Expected one native POST form.');
$check($xpath->query($form . '[@action="https://formsubmit.co/cruchristopherjohnhoylar@gmail.com"]')->length === 1,
    'FormSubmit destination must match the portfolio inbox.');
foreach (['name', 'email', 'message', 'privacyConsent'] as $name) {
    $check($xpath->query($form . '//*[@name="' . $name . '" and @required]')->length === 1, 'Missing required field ' . $name . '.');
}
$check($xpath->query($form . '//select[@name="projectBrief" and not(@required)]')->length === 1, 'The optional project brief must be included in the submitted data.');
$check($xpath->query($form . '//*[@name="_autoresponse" and @type="hidden"]')->length === 1, 'The confirmation request is missing.');
$check($xpath->query($form . '//*[@name="_honey" and @tabindex="-1"]')->length === 1, 'The honeypot is missing or keyboard-focusable.');
$check($xpath->query($form . '//a[@href="privacy.php"]')->length === 1, 'The privacy link is missing.');
$check($xpath->query($form . '//*[@name="csrfToken"]')->length === 0, 'A retired server-side token is present.');
echo "PASS: $checks contact form checks. No messages sent." . PHP_EOL;
