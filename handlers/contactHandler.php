<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/appConfig.php';
require_once __DIR__ . '/../includes/contactMailer.php';

startAppSession();

/** Use JSON for the enhanced form and a readable page for ordinary form posts. */
function sendContactResponse(int $statusCode, bool $success, string $message, array $errors = []): void
{
    http_response_code($statusCode);
    header('Cache-Control: no-store');
    header('X-Content-Type-Options: nosniff');

    $acceptHeader = (string) ($_SERVER['HTTP_ACCEPT'] ?? '');
    if (stripos($acceptHeader, 'application/json') !== false) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'errors' => (object) $errors,
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        exit;
    }

    header('Content-Type: text/html; charset=UTF-8');
    $pageTitle = $success ? 'Message sent' : 'Message could not be sent';
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">';
    echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
    echo '<title>' . escapeHtml($pageTitle) . ' | Christopher John Cruz</title>';
    echo '<link rel="stylesheet" href="../assets/css/fonts.css">';
    echo '<style>body{margin:0;padding:5rem 1.5rem;background:#f5f9ff;color:#172033;font:1rem/1.7 Poppins,sans-serif}main{box-sizing:border-box;max-width:38rem;margin:auto;padding:2rem;background:#fff;border:1px solid #e2e8f0;border-radius:1.25rem}h1{font-size:1.8rem;line-height:1.25}a{color:#245ec9;text-underline-offset:.2em}a:focus-visible{outline:3px solid #8b5cf6;outline-offset:5px}li{margin:.4rem 0}</style>';
    echo '</head><body><main><h1>' . escapeHtml($pageTitle) . '</h1>';
    echo '<p>' . escapeHtml($message) . '</p>';
    if ($errors !== []) {
        echo '<ul>';
        foreach ($errors as $errorMessage) {
            echo '<li>' . escapeHtml($errorMessage) . '</li>';
        }
        echo '</ul>';
    }
    echo '<p><a href="../index.php#contact">Return to the contact form</a></p>';
    echo '</main></body></html>';
    exit;
}

function getContactValue(string $fieldName): string
{
    $fieldValue = $_POST[$fieldName] ?? '';
    return is_string($fieldValue) ? trim($fieldValue) : '';
}

function getContactTextLength(string $value): int
{
    $characterCount = preg_match_all('/./us', $value);
    return $characterCount === false ? strlen($value) : $characterCount;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    header('Allow: POST');
    sendContactResponse(405, false, 'Please use the contact form to send a message.');
}

$submittedToken = getContactValue('csrfToken');
$sessionToken = $_SESSION['csrfToken'] ?? '';
if (!is_string($sessionToken) || $sessionToken === '' || !hash_equals($sessionToken, $submittedToken)) {
    sendContactResponse(403, false, 'Your form session has expired. Refresh the portfolio and try again.');
}

// A filled hidden field is rejected honestly instead of claiming delivery.
if (getContactValue('website') !== '' || (isset($_POST['website']) && !is_string($_POST['website']))) {
    sendContactResponse(422, false, 'The message could not be accepted. Please use the contact form or email directly.');
}

$name = getContactValue('name');
$email = getContactValue('email');
$message = getContactValue('message');
$errors = [];

if ($name === '') {
    $errors['name'] = 'Please enter your name.';
} elseif (getContactTextLength($name) > 100) {
    $errors['name'] = 'Please keep your name to 100 characters or fewer.';
} elseif (preg_match('//u', $name) !== 1 || preg_match('/[\x00-\x1F\x7F]/', $name)) {
    $errors['name'] = 'Please enter a valid name without line breaks.';
}

if ($email === '') {
    $errors['email'] = 'Please enter your email address.';
} elseif (strlen($email) > 254 || preg_match('/[\r\n\x00]/', $email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Please enter a valid email address.';
}

if ($message === '') {
    $errors['message'] = 'Please enter your message.';
} elseif (getContactTextLength($message) < 10 || getContactTextLength($message) > 5000) {
    $errors['message'] = 'Please write a message between 10 and 5,000 characters.';
} elseif (preg_match('//u', $message) !== 1 || preg_match('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', $message)) {
    $errors['message'] = 'Please remove unsupported control characters from your message.';
}

if ($errors !== []) {
    sendContactResponse(422, false, 'Please check the highlighted fields and try again.', $errors);
}

$messageFingerprint = hash('sha256', json_encode([$name, $email, $message], JSON_UNESCAPED_UNICODE));
$lastContactAt = (int) ($_SESSION['lastContactAt'] ?? 0);
$elapsedSeconds = time() - $lastContactAt;
if (isset($_SESSION['lastContactFingerprint'])
    && is_string($_SESSION['lastContactFingerprint'])
    && hash_equals($_SESSION['lastContactFingerprint'], $messageFingerprint)
    && $elapsedSeconds < 600) {
    sendContactResponse(409, false, 'This message has already been accepted. Please allow time for a reply before sending it again.');
}
if ($lastContactAt > 0 && $elapsedSeconds < 60) {
    header('Retry-After: ' . max(1, 60 - $elapsedSeconds));
    sendContactResponse(429, false, 'Please wait one minute between messages.');
}

try {
    $contactConfig = getContactConfig();
} catch (Throwable $configError) {
    error_log('Portfolio contact email configuration is invalid.');
    sendContactResponse(503, false, 'Email is temporarily unavailable. Please email cruchristopherjohnhoylar@gmail.com directly.');
}

if (!$contactConfig['enabled']) {
    sendContactResponse(503, false, 'Email is temporarily unavailable. Please email cruchristopherjohnhoylar@gmail.com directly.');
}

try {
    if (!sendContactMessage($contactConfig, $name, $email, $message)) {
        throw new RuntimeException('The contact relay did not accept the message.');
    }
} catch (Throwable $mailError) {
    // Keep visitor content and provider diagnostics out of logs and responses.
    error_log('Portfolio contact relay could not accept the message.');
    sendContactResponse(502, false, 'Your message could not be sent. Please try again later or email cruchristopherjohnhoylar@gmail.com directly.');
}

// Only relay-accepted messages start the cooldown; failed attempts remain retryable.
$_SESSION['lastContactAt'] = time();
$_SESSION['lastContactFingerprint'] = $messageFingerprint;
sendContactResponse(200, true, 'Your message has been submitted. Thank you for reaching out!');
