<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

// Optional local URL checks the rendered form and the contact session response headers.
$checks = 0;
$check = static function (bool $condition, string $message) use (&$checks): void {
    $checks++;
    if (!$condition) {
        fwrite(STDERR, 'FAIL: ' . $message . PHP_EOL);
        exit(1);
    }
};

$fixtureKey = 'test-brevo-key-must-not-appear-in-html';
$preview = isset($argv[1]);
if ($preview) {
    $url = $argv[1];
    $parts = parse_url($url);
    $check(is_array($parts) && in_array($parts['scheme'] ?? '', ['http', 'https'], true)
        && in_array($parts['host'] ?? '', ['localhost', '127.0.0.1', '[::1]'], true),
        'Use a local preview URL.');
    $previewHtml = file_get_contents($url, false, stream_context_create(['http' => ['follow_location' => 0, 'timeout' => 5]]));
    $check(is_string($previewHtml), 'Local preview did not return a page.');
    $pages = ['preview' => $previewHtml];
} else {
    $settings = [
        'unconfigured' => ['BREVO_API_KEY' => '', 'BREVO_SENDER_EMAIL' => '', 'BREVO_SENDER_NAME' => '', 'CONTACT_OWNER_EMAIL' => ''],
        'configured' => ['BREVO_API_KEY' => $fixtureKey, 'BREVO_SENDER_EMAIL' => 'sender@example.test',
            'BREVO_SENDER_NAME' => 'Test Sender', 'CONTACT_OWNER_EMAIL' => 'owner@example.test'],
    ];
    $pages = [];
    foreach ($settings as $mode => $overrides) {
        // Fresh processes isolate credential configuration. Both requests are GETs.
        $command = [PHP_BINARY, '-d', 'session.save_path=' . sys_get_temp_dir(),
            '-r', '$_SERVER["REQUEST_METHOD"] = "GET"; require $argv[1];',
            dirname(__DIR__) . '/index.php'];
        $process = proc_open($command, [['pipe', 'r'], ['pipe', 'w'], ['redirect', 1]], $pipes, null,
            array_replace(getenv(), $overrides));
        $check(is_resource($process), "Could not render the $mode contact fixture.");
        fclose($pipes[0]);
        $pages[$mode] = stream_get_contents($pipes[1]);
        fclose($pipes[1]);
        $check(proc_close($process) === 0 && is_string($pages[$mode]), "The $mode contact fixture failed.");
    }
}

foreach ($pages as $mode => $html) {
    $previous = libxml_use_internal_errors(true);
    $document = new DOMDocument();
    $document->loadHTML('<?xml encoding="UTF-8">' . $html);
    libxml_clear_errors();
    libxml_use_internal_errors($previous);
    $xpath = new DOMXPath($document);
    $form = '//form[@id="contact-form"]';
    $check($xpath->query($form)->length === 1 && $xpath->query($form . '[@method="post"]')->length === 1,
        'Expected one native POST contact form.');
    $check($xpath->query($form . '[@action="index.php#contact" and @data-brevo-contact]')->length === 1,
        'The contact form must submit to the local PHP backend.');
    $check($xpath->query($form . '//*[@name="contactAction" and @value="send" and @type="hidden"]')->length === 1,
        'The local contact action is missing.');
    $tokens = $xpath->query($form . '//*[@name="contactToken" and @type="hidden"]');
    $check($tokens->length === 1, 'The local contact token field is missing.');
    $token = $tokens->item(0)->getAttribute('value');
    $configured = $token !== '';
    if (!$preview) {
        $check($configured === ($mode === 'configured'), "Unexpected contact availability in the $mode fixture.");
    }
    $check(!$configured || preg_match('/^[a-f0-9]{64}$/', $token) === 1, 'Invalid contact session token.');
    $check($xpath->query($form . '//*[@data-submit-button and @disabled]')->length === ($configured ? 0 : 1),
        'The send button must reflect contact availability.');
    $check($xpath->query($form . '//*[@name="_autoresponse" or @name="_subject" or @name="_next"]')->length === 0,
        'The form must not expose third-party submission settings.');
    $check(!str_contains($html, $fixtureKey), 'The API key must never appear in HTML.');
    if ($preview && !$configured) {
        $check(preg_grep('/^Set-Cookie:/i', $http_response_header ?? []) === [], 'Unconfigured public home set a cookie.');
    }
    foreach (['name', 'email', 'message', 'privacyConsent'] as $name) {
        $check($xpath->query($form . '//*[@name="' . $name . '" and @required]')->length === 1, 'Missing required field ' . $name . '.');
    }
    $check($xpath->query($form . '//select[@name="projectBrief" and not(@required)]')->length === 1,
        'The optional project brief must be included in the submitted data.');
    $check($xpath->query($form . '//*[@name="_honey" and @tabindex="-1"]')->length === 1,
        'The honeypot is missing or keyboard-focusable.');
    $check($xpath->query($form . '//a[@href="privacy.php"]')->length === 1, 'The privacy link is missing.');
    $check($xpath->query($form . '//*[@name="csrfToken"]')->length === 0, 'A retired server-side token is present.');
}
echo "PASS: $checks contact form checks. No messages sent." . PHP_EOL;
