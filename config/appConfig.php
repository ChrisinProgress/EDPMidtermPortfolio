<?php
declare(strict_types=1);

/** Start the cookie-backed session before any page output. */
function startAppSession(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $isHttps = isset($_SERVER['HTTPS'])
        && $_SERVER['HTTPS'] !== ''
        && strtolower((string) $_SERVER['HTTPS']) !== 'off';

    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    session_name('cjcPortfolioSession');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'secure' => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

function getCsrfToken(): string
{
    startAppSession();

    if (!isset($_SESSION['csrfToken']) || !is_string($_SESSION['csrfToken'])) {
        $_SESSION['csrfToken'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrfToken'];
}

function escapeHtml($value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Password-free contact relay; the recipient cannot be changed by form input. */
function getContactConfig(): array
{
    $enabledValue = getenv('PORTFOLIO_CONTACT_ENABLED');
    $recipient = 'cruchristopherjohnhoylar@gmail.com';
    $siteUrl = getenv('PORTFOLIO_SITE_URL');
    if ($siteUrl === false || trim($siteUrl) === '') {
        $host = (string) ($_SERVER['HTTP_HOST'] ?? 'localhost');
        if (preg_match('/\A(?:[a-z0-9.-]+|\[[a-f0-9:]+\])(?::[0-9]{1,5})?\z/i', $host) !== 1) {
            throw new RuntimeException('Invalid website address.');
        }
        $isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== '' && strtolower((string) $_SERVER['HTTPS']) !== 'off';
        $scriptPath = (string) ($_SERVER['SCRIPT_NAME'] ?? '/MidtermProjectPortfolio/handlers/contactHandler.php');
        $basePath = PHP_SAPI === 'cli'
            ? '/' . basename(dirname(__DIR__))
            : rtrim(str_replace('\\', '/', dirname($scriptPath, 2)), '/.');
        $siteUrl = ($isHttps ? 'https://' : 'http://') . $host . $basePath . '/';
    }
    $siteUrl = trim($siteUrl);
    $siteParts = parse_url($siteUrl);
    if (!filter_var($siteUrl, FILTER_VALIDATE_URL) || !is_array($siteParts)
        || !in_array($siteParts['scheme'] ?? '', ['http', 'https'], true)
        || isset($siteParts['user']) || isset($siteParts['pass'])
        || preg_match('/[\r\n\x00]/', $siteUrl)) {
        throw new RuntimeException('Invalid website address.');
    }

    return [
        'enabled' => $enabledValue === false || filter_var($enabledValue, FILTER_VALIDATE_BOOLEAN),
        'recipient' => $recipient,
        'endpoint' => 'https://formsubmit.co/ajax/' . $recipient,
        'siteUrl' => $siteUrl,
    ];
}
