<?php
declare(strict_types=1);

// Run: PORTFOLIO_CONTACT_ENABLED=false php tools/verifyContact.php [http://127.0.0.1:8000]
// The preview server MUST also run with PORTFOLIO_CONTACT_ENABLED=false.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/includes/contactMailer.php';

final class ContactVerificationFailure extends RuntimeException {}

$checkCount = 0;
$check = static function (bool $condition, string $description) use (&$checkCount): void {
    $checkCount++;
    if (!$condition) {
        throw new ContactVerificationFailure($description);
    }
};
$sessions = [];
$failure = '';

try {
    $check(strtolower(trim((string) getenv('PORTFOLIO_CONTACT_ENABLED'))) === 'false',
        'Set PORTFOLIO_CONTACT_ENABLED=false for this script and the preview server before testing.');

    $fixtureName = "Portfolio Test O'Connor • 雪";
    $fixtureEmail = 'portfolio-test@example.test';
    $fixtureMessage = "A synthetic client inquiry: I'd like a website.\n<strong>Literal HTML stays text.</strong> 雪";
    $payload = buildContactPayload($fixtureName, $fixtureEmail, $fixtureMessage);
    $check(($payload['name'] ?? null) === $fixtureName
        && ($payload['email'] ?? null) === $fixtureEmail
        && ($payload['message'] ?? null) === $fixtureMessage,
        'The relay payload must exactly preserve the name, email, and message including apostrophes, Unicode, and literal HTML.');
    $check(is_string($payload['_subject'] ?? null) && $payload['_subject'] !== ''
        && strpos($payload['_subject'], $fixtureName) === false
        && strpos($payload['_subject'], $fixtureEmail) === false,
        'The relay subject must be static and omit visitor-supplied header text.');
    $check(($payload['_template'] ?? null) === 'table', 'Contact notification emails must use the table template.');
    $check(count($payload) === 5, 'The payload must contain only the three form fields, subject, and template.');

    foreach ([
        ['status' => 200, 'body' => '{"success":true}', 'expected' => true, 'case' => 'boolean success'],
        ['status' => 200, 'body' => '{"success":"true"}', 'expected' => true, 'case' => 'string success'],
        ['status' => 201, 'body' => '{"success":true}', 'expected' => true, 'case' => 'other successful HTTP status'],
        ['status' => 200, 'body' => '{"success":false}', 'expected' => false, 'case' => 'boolean rejection'],
        ['status' => 200, 'body' => '{"success":"false"}', 'expected' => false, 'case' => 'string rejection'],
        ['status' => 200, 'body' => '{"success":1}', 'expected' => false, 'case' => 'numeric truthiness'],
        ['status' => 200, 'body' => '{"success":"TRUE"}', 'expected' => false, 'case' => 'unexpected success string'],
        ['status' => 200, 'body' => '{"success":null}', 'expected' => false, 'case' => 'null success'],
        ['status' => 200, 'body' => '{}', 'expected' => false, 'case' => 'missing success'],
        ['status' => 200, 'body' => 'null', 'expected' => false, 'case' => 'null JSON'],
        ['status' => 200, 'body' => 'true', 'expected' => false, 'case' => 'scalar JSON'],
        ['status' => 200, 'body' => '[]', 'expected' => false, 'case' => 'empty array'],
        ['status' => 200, 'body' => 'not JSON', 'expected' => false, 'case' => 'malformed JSON'],
        ['status' => 204, 'body' => '', 'expected' => false, 'case' => 'empty response'],
        ['status' => 0, 'body' => '{"success":true}', 'expected' => false, 'case' => 'transport failure'],
        ['status' => 199, 'body' => '{"success":true}', 'expected' => false, 'case' => 'informational response'],
        ['status' => 302, 'body' => '{"success":true}', 'expected' => false, 'case' => 'redirect response'],
        ['status' => 400, 'body' => '{"success":true}', 'expected' => false, 'case' => 'client failure'],
        ['status' => 503, 'body' => '{"success":true}', 'expected' => false, 'case' => 'provider unavailable'],
    ] as $fixture) {
        $check(parseContactRelayResponse($fixture['status'], $fixture['body']) === $fixture['expected'],
            'Relay response parsing must handle ' . $fixture['case'] . ' honestly.');
    }

    $check(extension_loaded('curl'), 'PHP cURL is required for local HTTP checks.');
    $baseUrl = rtrim($argv[1] ?? 'http://127.0.0.1:8000', '/');
    $urlParts = parse_url($baseUrl);
    $check(is_array($urlParts)
        && in_array($urlParts['scheme'] ?? '', ['http', 'https'], true)
        && in_array(strtolower($urlParts['host'] ?? ''), ['localhost', '127.0.0.1', '[::1]', '::1'], true)
        && !isset($urlParts['user']) && !isset($urlParts['pass'])
        && !isset($urlParts['query']) && !isset($urlParts['fragment']),
        'Use a loopback preview URL without credentials, query parameters, or a fragment.');

    $newSession = static function () use (&$sessions) {
        $session = curl_init();
        if ($session === false) {
            throw new ContactVerificationFailure('Could not start a local HTTP test session.');
        }
        curl_setopt_array($session, [
            CURLOPT_COOKIEFILE => '', CURLOPT_RETURNTRANSFER => true, CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_TIMEOUT => 15, CURLOPT_FOLLOWLOCATION => false, CURLOPT_PROXY => '',
        ]);
        $sessions[] = $session;
        return $session;
    };

    $request = static function ($session, string $path, ?array $fields = null, string $accept = 'application/json') use ($baseUrl, $check): array {
        curl_setopt_array($session, [
            CURLOPT_URL => $baseUrl . '/' . ltrim($path, '/'),
            CURLOPT_HTTPHEADER => ['Accept: ' . $accept],
        ]);
        if ($fields === null) {
            curl_setopt($session, CURLOPT_HTTPGET, true);
        } else {
            curl_setopt($session, CURLOPT_POST, true);
            curl_setopt($session, CURLOPT_POSTFIELDS, http_build_query($fields, '', '&', PHP_QUERY_RFC3986));
        }
        $body = curl_exec($session);
        if ($body === false) {
            throw new ContactVerificationFailure('Check that the contact-disabled local preview is running.');
        }
        foreach ([dirname(__DIR__), 'Fatal error:', 'Stack trace:'] as $privateDetail) {
            $check(stripos($body, $privateDetail) === false,
                'HTTP responses must not expose private paths or exception traces.');
        }
        return [
            'status' => (int) curl_getinfo($session, CURLINFO_RESPONSE_CODE),
            'contentType' => (string) curl_getinfo($session, CURLINFO_CONTENT_TYPE), 'body' => $body,
        ];
    };

    $getToken = static function ($session) use ($request, $check): string {
        $page = $request($session, '', null, 'text/html');
        $check($page['status'] === 200, 'The portfolio page must load before submitting.');
        preg_match('/name="csrfToken"\s+value="([^"]+)"/', $page['body'], $tokenMatch);
        $token = html_entity_decode($tokenMatch[1] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $check($token !== '', 'The contact form must include a session CSRF token.');
        return $token;
    };

    $assertJsonFailure = static function (array $response, int $status, string $description) use ($check): array {
        $json = json_decode($response['body'], true);
        $check($response['status'] === $status && is_array($json) && ($json['success'] ?? null) === false, $description);
        $check(stripos($response['contentType'], 'application/json') === 0,
            'Enhanced form responses must use the JSON content type.');
        return $json;
    };

    $session = $newSession();
    $handlerPath = 'handlers/contactHandler.php';
    $assertJsonFailure($request($session, $handlerPath), 405, 'Handler GET requests must return 405.');
    $token = $getToken($session);
    $validFields = [
        'name' => $fixtureName, 'email' => $fixtureEmail, 'message' => $fixtureMessage,
        'csrfToken' => $token, 'website' => '',
    ];
    $assertJsonFailure($request($session, $handlerPath, array_replace($validFields, ['csrfToken' => 'invalid-test-token'])),
        403, 'An invalid CSRF token must return 403.');

    foreach ([
        'missing name' => ['name' => ''],
        'oversized name' => ['name' => str_repeat('a', 101)],
        'name header injection' => ['name' => "Visitor\r\nBcc: other@example.test"],
        'malformed email' => ['email' => 'invalid@'],
        'email header injection' => ['email' => "visitor@example.test\r\nBcc: other@example.test"],
        'short message' => ['message' => 'short'],
        'oversized message' => ['message' => str_repeat('a', 5001)],
        'message control character' => ['message' => "Invalid\0 message text"],
        'non-scalar name' => ['name' => ['Visitor']],
        'filled honeypot' => ['website' => 'synthetic-bot'],
        'non-scalar honeypot' => ['website' => ['synthetic-bot']],
    ] as $case => $overrides) {
        $assertJsonFailure($request($session, $handlerPath, array_replace($validFields, $overrides)),
            422, 'Validation must return 422 for ' . $case . '.');
    }

    for ($attempt = 0; $attempt < 2; $attempt++) {
        $json = $assertJsonFailure($request($session, $handlerPath, $validFields), 503,
            'Contact-disabled valid requests must report unavailable delivery without false success or a failure cooldown.');
        $check(is_string($json['message'] ?? null) && $json['message'] !== '',
            'Unavailable delivery must provide a readable visitor message.');
    }

    $fallbackSession = $newSession();
    $fallbackToken = $getToken($fallbackSession);
    $response = $request($fallbackSession, $handlerPath, array_replace($validFields, ['csrfToken' => $fallbackToken]), 'text/html');
    $check($response['status'] === 503 && stripos($response['contentType'], 'text/html') === 0
        && strpos($response['body'], '<h1>Message could not be sent</h1>') !== false,
        'Ordinary form submission must show a readable delivery failure page with status 503.');
    $check(strpos($response['body'], 'Return to the contact form') !== false,
        'The ordinary form response must let visitors return to their form.');
    $check($request($session, 'tools/verifyContact.php')['status'] === 404,
        'The verification script must not run through the web server.');
} catch (Throwable $error) {
    $failure = $error instanceof ContactVerificationFailure
        ? $error->getMessage()
        : 'Verification could not complete. Check the contact-disabled local preview.';
} finally {
    foreach ($sessions as $session) {
        curl_close($session);
    }
}

if ($failure !== '') {
    fwrite(STDERR, 'Contact verification failed: ' . $failure . PHP_EOL);
    exit(1);
}
fwrite(STDOUT, 'Contact verification passed: ' . $checkCount . ' checks. No external relay request or email was sent.' . PHP_EOL);
