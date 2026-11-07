<?php
declare(strict_types=1);

function escapeHtml($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function brevoContactConfig(): ?array
{
    $key = trim((string) getenv('BREVO_API_KEY'));
    $sender = trim((string) getenv('BREVO_SENDER_EMAIL'));
    $name = trim((string) getenv('BREVO_SENDER_NAME'));
    $owner = trim((string) getenv('CONTACT_OWNER_EMAIL'));

    if ($key === '' || preg_match('/[\r\n]/', $key) || $name === '' ||
        !filter_var($sender, FILTER_VALIDATE_EMAIL) || !filter_var($owner, FILTER_VALIDATE_EMAIL)) {
        return null;
    }

    return ['key' => $key, 'sender' => $sender, 'name' => $name, 'owner' => $owner];
}
