<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/../config/appConfig.php';
require_once __DIR__ . '/../includes/contactDelivery.php';

ini_set('session.save_path', sys_get_temp_dir());
putenv('BREVO_API_KEY=local-test-placeholder');
putenv('BREVO_SENDER_EMAIL=sender@example.com');
putenv('BREVO_SENDER_NAME=Christopher John Cruz');
putenv('CONTACT_OWNER_EMAIL=owner@example.com');

function checkContact(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$mail = [];
$outcomes = [];
$transport = static function (string $key, array $payload, string $kind) use (&$mail, &$outcomes): string {
    checkContact($key === 'local-test-placeholder', 'Backend did not use the configured API key.');
    $mail[] = [$kind, $payload];
    return array_shift($outcomes) ?? 'accepted';
};

$request = [
    'name' => 'Portfolio Test User',
    'email' => 'visitor@example.com',
    'projectBrief' => 'Brevo Email Integration Test',
    'message' => 'This is an isolated backend test.',
    'privacyConsent' => 'Agreed to inquiry processing',
    'contactToken' => contactFormToken(),
];

foreach (['name' => '', 'email' => 'invalid-address', 'message' => '', 'privacyConsent' => '', '_honey' => 'bot'] as $field => $value) {
    [$status, $result, $values] = submitPortfolioContact(array_replace($request, [$field => $value]), $transport);
    checkContact($status === 422 && !$result['ok'] && $mail === [], "Invalid {$field} reached Brevo.");
}
[$status, $result] = submitPortfolioContact(array_replace($request, ['contactToken' => 'expired']), $transport);
checkContact($status === 403 && isset($result['nextToken']) && $mail === [], 'Invalid token reached Brevo.');

[$status, $result, $values] = submitPortfolioContact($request, $transport);
checkContact($status === 200 && $result['message'] === CONTACT_SEND_SUCCESS && $values === [], 'Accepted inquiry did not report success.');
checkContact(array_column($mail, 0) === ['owner', 'visitor'], 'Expected two separate messages, with the owner first.');
[$owner, $visitor] = array_column($mail, 1);
checkContact($owner['to'][0]['email'] === 'owner@example.com' && $owner['replyTo']['email'] === $request['email'], 'Owner recipient or Reply-To is incorrect.');
checkContact($owner['subject'] === 'New Portfolio Inquiry — Christopher John Cruz', 'Incorrect owner subject.');
foreach ([$request['name'], $request['email'], $request['projectBrief'], $request['message'], 'Submitted:'] as $expected) {
    checkContact(str_contains($owner['textContent'], $expected), 'Owner email is missing inquiry details.');
}
checkContact($visitor['to'][0]['email'] === $request['email'] && $visitor['subject'] === 'Thank You for Contacting Christopher John Cruz', 'Incorrect visitor recipient or subject.');
checkContact(!str_contains($visitor['textContent'], $request['message']) && !str_contains($visitor['textContent'], $request['projectBrief']), 'Acknowledgment contains owner-only data.');

submitPortfolioContact($request, $transport);
submitPortfolioContact(array_replace($request, ['contactToken' => $result['nextToken']]), $transport);
checkContact(count($mail) === 2, 'Retrying an accepted inquiry duplicated a send.');
[$status] = submitPortfolioContact(array_replace($request, ['message' => 'Different message with a used token.']), $transport);
checkContact($status === 409 && count($mail) === 2, 'A used token accepted a different payload.');

$outcomes = ['accepted', 'rejected'];
$partial = array_replace($request, ['contactToken' => contactFormToken(), 'projectBrief' => '', 'message' => 'Test a rejected acknowledgment.']);
[$status, $result] = submitPortfolioContact($partial, $transport);
checkContact($status === 200 && str_contains($result['message'], 'could not confirm the acknowledgment'), 'Partial failure is inaccurate.');
checkContact(!str_contains($mail[2][1]['textContent'], 'Project brief:'), 'Empty brief was not omitted.');
submitPortfolioContact($partial, $transport);
checkContact(count($mail) === 4, 'Retrying after acknowledgment failure duplicated the inquiry.');

$outcomes = ['uncertain'];
$uncertain = array_replace($request, ['contactToken' => contactFormToken(), 'message' => 'Test an uncertain owner response.']);
[$status, $result, $values] = submitPortfolioContact($uncertain, $transport);
checkContact($status === 502 && !$result['ok'] && $result['message'] === CONTACT_SEND_UNCONFIRMED && $values['message'] === $uncertain['message'], 'Uncertain response is inaccurate or lost draft data.');
submitPortfolioContact($uncertain, $transport);
checkContact(count($mail) === 5, 'Uncertain owner response caused an acknowledgment or duplicate.');

$outcomes = ['rejected'];
$rejected = array_replace($request, ['contactToken' => contactFormToken(), 'message' => 'Test a rejected owner response.']);
[$status, $result] = submitPortfolioContact($rejected, $transport);
checkContact($status === 502 && $result['message'] === CONTACT_SEND_ERROR && count($mail) === 6, 'Rejected owner request reported acceptance or sent an acknowledgment.');
[$status] = submitPortfolioContact($rejected, $transport);
checkContact($status === 429 && count($mail) === 6, 'Contact rate limit failed.');

echo "PASS: Brevo payloads, validation, tokens, rate limiting, deduplication, and partial/uncertain outcomes. No emails sent.\n";
