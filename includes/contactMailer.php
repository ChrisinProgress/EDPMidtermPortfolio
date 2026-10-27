<?php
declare(strict_types=1);

/** Only the visitor's three fields and fixed email presentation settings are sent. */
function buildContactPayload(string $name, string $email, string $message): array
{
    return [
        'name' => $name,
        'email' => $email,
        'message' => $message,
        '_subject' => 'New client inquiry from your portfolio',
        '_template' => 'table',
    ];
}

/** A provider rejection, invalid response, or network failure never means success. */
function parseContactRelayResponse(int $statusCode, string $body): bool
{
    if ($statusCode < 200 || $statusCode >= 300) {
        return false;
    }
    $response = json_decode($body, true);
    return is_array($response)
        && in_array($response['success'] ?? null, [true, 'true'], true);
}

/** Forward through FormSubmit without an email password or local message storage. */
function sendContactMessage(array $config, string $name, string $email, string $message): bool
{
    if (!function_exists('curl_init')) {
        throw new RuntimeException('The contact relay requires PHP cURL.');
    }
    // Keep the destination fixed even if additional fields are posted to the form.
    $endpoint = 'https://formsubmit.co/ajax/cruchristopherjohnhoylar@gmail.com';
    if (($config['endpoint'] ?? '') !== $endpoint) {
        throw new RuntimeException('Invalid contact relay destination.');
    }
    $connection = curl_init($endpoint);
    if ($connection === false) {
        throw new RuntimeException('The contact relay is unavailable.');
    }
    try {
        curl_setopt_array($connection, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode(buildContactPayload($name, $email, $message), JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'],
            CURLOPT_USERAGENT => 'CJC-Portfolio-Contact/1.0',
            CURLOPT_REFERER => $config['siteUrl'],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 8,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => false,
            CURLOPT_PROTOCOLS => CURLPROTO_HTTPS,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
        ]);
        $body = curl_exec($connection);
        return is_string($body)
            && parseContactRelayResponse((int) curl_getinfo($connection, CURLINFO_RESPONSE_CODE), $body);
    } finally {
        curl_close($connection);
    }
}
