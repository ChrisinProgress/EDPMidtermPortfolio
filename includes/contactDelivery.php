<?php
declare(strict_types=1);

const CONTACT_SESSION_TTL = 1200;
const CONTACT_SEND_ERROR = 'Unable to send your message. Please try again.';
const CONTACT_SEND_SUCCESS = 'Your message has been submitted successfully! Thank you for reaching out.';
const CONTACT_SEND_UNCONFIRMED = 'We could not confirm that your inquiry was accepted. To avoid a duplicate, please use the email link to follow up.';

function startContactSession(): bool
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return true;
    }

    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';
    session_name('cjcPortfolioSession');
    return session_start([
        'use_only_cookies' => true,
        'use_strict_mode' => true,
        'cookie_secure' => $isHttps,
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
        'gc_maxlifetime' => CONTACT_SESSION_TTL,
    ]);
}

function pruneContactSession(int $now): void
{
    $_SESSION['contactTokens'] = array_filter($_SESSION['contactTokens'] ?? [], static fn($entry) => $entry['at'] > $now - CONTACT_SESSION_TTL);
    $_SESSION['contactAttempts'] = array_filter($_SESSION['contactAttempts'] ?? [], static fn($at) => $at > $now - 600);
    $_SESSION['contactResults'] = array_filter($_SESSION['contactResults'] ?? [], static fn($entry) => $entry['at'] > $now - CONTACT_SESSION_TTL);
}

function issueContactToken(int $now): string
{
    $token = bin2hex(random_bytes(32));
    $_SESSION['contactTokens'][$token] = ['at' => $now];
    $_SESSION['contactTokens'] = array_slice($_SESSION['contactTokens'], -16, null, true);
    return $token;
}

function contactFormToken(): string
{
    if (!startContactSession()) {
        return '';
    }
    $now = time();
    pruneContactSession($now);
    $token = issueContactToken($now);
    session_write_close();
    return $token;
}

function portfolioContactFields(array $input): array
{
    $limits = ['name' => 100, 'email' => 254, 'projectBrief' => 120, 'message' => 5000, 'privacyConsent' => 64];
    $values = [];
    $valid = true;
    foreach ($limits as $field => $limit) {
        $raw = $input[$field] ?? '';
        if (!is_string($raw) || strlen($raw) > $limit * 4) {
            $values[$field] = is_string($raw) ? trim(substr($raw, 0, $limit * 4)) : '';
            $valid = false;
            continue;
        }
        $values[$field] = trim($raw);
        $length = preg_match_all('/./us', $values[$field]);
        if ($length === false || $length > $limit) {
            $valid = false;
        }
    }
    $valid = $valid && $values['name'] !== '' && !preg_match('/[\x00-\x1F\x7F]/', $values['name']) &&
        !preg_match('/[\x00-\x1F\x7F]/', $values['projectBrief']) && preg_match_all('/./us', $values['message']) >= 10 &&
        strlen($values['email']) <= 254 && filter_var($values['email'], FILTER_VALIDATE_EMAIL) &&
        $values['privacyConsent'] === 'Agreed to inquiry processing' && ($input['_honey'] ?? '') === '';
    return [$valid, $values];
}

// Return accepted only when Brevo supplies a message ID. A timed-out request
// may have reached Brevo, so its outcome is uncertain and must not be retried blindly.
function sendPortfolioBrevoMail(string $key, array $mail, string $kind): string
{
    if (!function_exists('curl_init')) {
        error_log('Portfolio contact: PHP cURL is unavailable.');
        return 'rejected';
    }

    $curl = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($curl, [
        CURLOPT_POST => true,
        CURLOPT_HTTPHEADER => ['accept: application/json', 'content-type: application/json', 'api-key: ' . $key],
        CURLOPT_POSTFIELDS => json_encode($mail, JSON_INVALID_UTF8_SUBSTITUTE | JSON_THROW_ON_ERROR),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 4,
        CURLOPT_TIMEOUT => 8,
    ]);
    $raw = curl_exec($curl);
    $status = (int) curl_getinfo($curl, CURLINFO_RESPONSE_CODE);
    $transportError = curl_errno($curl);
    $response = is_string($raw) ? json_decode($raw, true) : null;
    $messageId = is_array($response) ? ($response['messageId'] ?? null) : null;
    if ($status >= 200 && $status < 300 && is_string($messageId) && $messageId !== '') {
        $safeId = preg_replace('/[^A-Za-z0-9.@_<>-]/', '', substr($messageId, 0, 255));
        error_log("Portfolio contact: Brevo {$kind} accepted, message ID {$safeId}.");
        return 'accepted';
    }

    $providerCode = is_array($response) ? ($response['code'] ?? null) : null;
    $safeCode = is_string($providerCode) ? preg_replace('/[^A-Za-z0-9_-]/', '', substr($providerCode, 0, 64)) : '';
    error_log("Portfolio contact: Brevo {$kind} response HTTP {$status}, cURL {$transportError}, code {$safeCode}.");
    return $status >= 400 && $status < 500 && $status !== 408 ? 'rejected' : 'uncertain';
}

function deliverPortfolioInquiry(array $config, array $values, callable $send): array
{
    $sender = ['email' => $config['sender'], 'name' => $config['name']];
    $submittedAt = (new DateTimeImmutable('now', new DateTimeZone('Asia/Manila')))->format('F j, Y, g:i A (T)');
    $brief = $values['projectBrief'] === '' ? '' : "\nProject brief: {$values['projectBrief']}";
    $ownerOutcome = $send($config['key'], [
        'sender' => $sender,
        'to' => [['email' => $config['owner']]],
        'replyTo' => ['email' => $values['email'], 'name' => $values['name']],
        'subject' => 'New Portfolio Inquiry — Christopher John Cruz',
        'textContent' => "New inquiry from the portfolio contact form.\n\n" .
            "Name: {$values['name']}\nEmail: {$values['email']}{$brief}\nSubmitted: {$submittedAt}\n\n" .
            "Message:\n{$values['message']}",
        'tags' => ['portfolio-inquiry'],
    ], 'owner');
    if ($ownerOutcome !== 'accepted') {
        return [false, $ownerOutcome === 'uncertain' ? CONTACT_SEND_UNCONFIRMED : CONTACT_SEND_ERROR, $ownerOutcome === 'uncertain'];
    }

    $visitorOutcome = $send($config['key'], [
        'sender' => $sender,
        'to' => [['email' => $values['email']]],
        'replyTo' => ['email' => $config['owner'], 'name' => $config['name']],
        'subject' => 'Thank You for Contacting Christopher John Cruz',
        'textContent' => "Hello,\n\nThank you for reaching out through my portfolio website!\n\n" .
            "Your message has been received successfully. I appreciate your interest and will get back to you as soon as possible.\n\n" .
            "Best regards,\n\nChristopher John Cruz\n\nPortfolio Website: https://christopherjohncruz.onrender.com",
        'tags' => ['portfolio-acknowledgment'],
    ], 'visitor');
    return [true, $visitorOutcome === 'accepted' ? CONTACT_SEND_SUCCESS :
        CONTACT_SEND_SUCCESS . ' We could not confirm the acknowledgment email. You do not need to resend your inquiry.', true];
}

function submitPortfolioContact(array $input, ?callable $send = null): array
{
    [$valid, $values] = portfolioContactFields($input);
    $config = brevoContactConfig();
    if ($config === null || !startContactSession()) {
        return [503, ['ok' => false, 'message' => CONTACT_SEND_ERROR], $values];
    }
    try {
        $now = time();
        pruneContactSession($now);
        $token = $input['contactToken'] ?? null;
        if (!is_string($token) || !isset($_SESSION['contactTokens'][$token])) {
            return [403, ['ok' => false, 'message' => 'The form expired. Please try again.', 'nextToken' => issueContactToken($now)], $values];
        }
        if (!$valid) {
            return [422, ['ok' => false, 'message' => 'Enter a valid name, email, and message, and agree to the Privacy Policy.'], $values];
        }

        $fingerprint = hash_hmac('sha256', json_encode($values, JSON_THROW_ON_ERROR), session_id());
        $tokenFingerprint = $_SESSION['contactTokens'][$token]['fingerprint'] ?? null;
        if ($tokenFingerprint !== null && !hash_equals($tokenFingerprint, $fingerprint)) {
            return [409, ['ok' => false, 'message' => 'The form was already used. Review your message and try again.', 'nextToken' => issueContactToken($now)], $values];
        }
        $previous = $_SESSION['contactResults'][$fingerprint] ?? null;
        if ($previous !== null) {
            $_SESSION['contactTokens'][$token]['fingerprint'] = $fingerprint;
            $result = ['ok' => $previous['ok'], 'message' => $previous['message'], 'nextToken' => issueContactToken($now)];
            return [$previous['ok'] ? 200 : 502, $result, $previous['ok'] ? [] : $values];
        }
        if (count($_SESSION['contactAttempts']) >= 4) {
            return [429, ['ok' => false, 'message' => 'Too many attempts. Please wait a few minutes and try again.'], $values];
        }

        // Keep the session locked until the send outcome is recorded, so simultaneous
        // requests with the same form data cannot both send an owner notification.
        $_SESSION['contactAttempts'][] = $now;
        [$ok, $message, $remember] = deliverPortfolioInquiry($config, $values, $send ?? 'sendPortfolioBrevoMail');
        if ($remember) {
            $_SESSION['contactResults'][$fingerprint] = ['at' => $now, 'ok' => $ok, 'message' => $message];
            $_SESSION['contactTokens'][$token]['fingerprint'] = $fingerprint;
        }
        $result = ['ok' => $ok, 'message' => $message, 'nextToken' => issueContactToken($now)];
        return [$ok ? 200 : 502, $result, $ok ? [] : $values];
    } finally {
        session_write_close();
    }
}
