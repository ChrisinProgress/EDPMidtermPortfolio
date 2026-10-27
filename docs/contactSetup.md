# Contact form setup

The contact form forwards the entered name, email address, and message to **cruchristopherjohnhoylar@gmail.com** through [FormSubmit](https://formsubmit.co/). It does not need a Gmail password, SMTP credential, database, or package installation.

## Activate email forwarding once

1. Start Apache in XAMPP and open [the portfolio](http://localhost/MidtermProjectPortfolio/).
2. Fill in the contact form and click **Send Message**.
3. Open **cruchristopherjohnhoylar@gmail.com** and find the FormSubmit activation email. Check Spam if it is missing.
4. Click the activation link in that email.
5. Submit another test message and confirm that the email contains the entered name, email, and message. You can reply to the notification to contact the visitor.

The first submission starts the activation process. Until the owner activates the form, forwarding is not ready for client use. A relay acceptance response does not guarantee inbox arrival.

## How it works

`pages/contact.php` submits to `handlers/contactHandler.php`. The handler validates the fields, checks its session CSRF token and hidden honeypot, and forwards only the contact fields plus a fixed subject and table-template setting. `includes/contactMailer.php` uses PHP cURL over HTTPS to the fixed endpoint:

```text
https://formsubmit.co/ajax/cruchristopherjohnhoylar@gmail.com
```

The email subject is **New client inquiry from your portfolio**. Form input cannot choose a different recipient, CC address, or forwarding endpoint. The enhanced form displays its response without leaving the page. Ordinary browser posts receive a readable HTML response and a return link.

The relay includes the website address as its Referer, which FormSubmit requires. It is detected from the PHP request. For a deployment behind a proxy or a different public URL, set `PORTFOLIO_SITE_URL` to the full public portfolio URL, such as `https://example.com/portfolio/`. Check activation again when moving to a new website address.

The application does not store messages in a database. **FormSubmit is an external service that processes the submitted details and documents a 30-day submission archive.** Review its [documentation](https://formsubmit.co/documentation) and privacy information before using the form for sensitive information.

## Server requirements and troubleshooting

Use PHP 8.0 or newer with cURL and OpenSSL enabled, a working certificate trust configuration, and outbound HTTPS access. XAMPP PHP 8.2.12 was used for local checks. MySQL is not required.

Forwarding is enabled by default. Set `PORTFOLIO_CONTACT_ENABLED=false` in the PHP server environment to disable outgoing requests during automated tests or maintenance. Remove that override or set it to `true` to enable forwarding again. Environment variables set in an unrelated terminal may not reach Apache.

If messages do not arrive:

- Complete the FormSubmit activation from the recipient inbox and check Spam.
- Confirm the site is opened through Apache or a PHP preview server, not a local file or static server.
- Check that the PHP process can reach FormSubmit over HTTPS and that cURL and OpenSSL are enabled.
- Check that forwarding has not been disabled through `PORTFOLIO_CONTACT_ENABLED`.
- Check provider availability and email filtering. The form reports relay failure instead of claiming the message was sent.

## Request contract

Submit a `POST` request to `handlers/contactHandler.php` with the same session cookie as the portfolio page:

| Field | Requirement |
| --- | --- |
| `name` | Required; at most 100 characters; no control characters or line breaks. |
| `email` | Required valid email; at most 254 bytes; no header line breaks. |
| `message` | Required; 10–5,000 characters; ordinary line breaks allowed. |
| `website` | Hidden honeypot; must remain empty. |
| `csrfToken` | Current session token rendered in the form. |

Enhanced requests send `Accept: application/json` and receive `{ "success": boolean, "message": string, "errors": object }`. Field errors use the input names as keys.

| HTTP status | Meaning |
| --- | --- |
| `200` | FormSubmit accepted the submission. |
| `403` | Invalid session token. |
| `405` | Unsupported request method. |
| `409` | Duplicate of a message accepted from this session within ten minutes. |
| `422` | Invalid fields or honeypot rejection. |
| `429` | The 60-second accepted-message cooldown is active. |
| `502` | Relay rejection or network failure. |
| `503` | Forwarding is disabled or unavailable. |

Only accepted submissions start the cooldown. A fingerprint and acceptance time are retained in the session to reduce accidental repeats; full message contents are not written to the application's database or logs. Sessions use HttpOnly and SameSite=Lax cookies, with Secure enabled when PHP receives HTTPS.

## Automated checks without outgoing email

Start the local preview with forwarding disabled:

```powershell
$env:PORTFOLIO_CONTACT_ENABLED = 'false'
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8000 -t .
```

In another PowerShell window, also set that environment variable and run:

```powershell
$env:PORTFOLIO_CONTACT_ENABLED = 'false'
& 'C:\xampp\php\php.exe' tools/verifyContact.php http://127.0.0.1:8000
```

The CLI-only verifier checks relay payloads, response parsing, validation, CSRF, disabled forwarding, and the ordinary HTML response. It does not send live email or require a database. Stop the disabled preview before testing actual delivery through Apache.
