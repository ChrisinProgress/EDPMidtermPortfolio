# Portfolio contact form

The contact form uses Brevo's [Transactional Email API](https://developers.brevo.com/reference/send-transac-email) over HTTPS. The PHP backend sends the owner inquiry first, with the visitor as Reply-To. After Brevo accepts the inquiry, PHP sends a separate acknowledgment to the visitor. The [Brevo Free plan](https://help.brevo.com/hc/en-us/articles/208589409-About-Brevo-s-pricing-plans) includes 300 sends per day across the account. An inquiry with an acknowledgment uses two sends.

## Configure Render

Verify a sender in [Brevo](https://help.brevo.com/hc/en-us/articles/208836149-Create-a-new-sender-From-name-and-From-email). Then set these variables in the Render web service's [Environment tab](https://render.com/docs/configure-environment-variables) and deploy.

| Variable | Value |
| --- | --- |
| `BREVO_API_KEY` | A private key created under Brevo SMTP & API → API Keys. |
| `BREVO_SENDER_EMAIL` | The sender address verified in Brevo. |
| `BREVO_SENDER_NAME` | Christopher John Cruz |
| `CONTACT_OWNER_EMAIL` | cruchristopherjohnhoylar@gmail.com |

Brevo may [rewrite a Gmail sender's visible From address](https://help.brevo.com/hc/en-us/articles/14925263522578-Comply-with-Gmail-Yahoo-and-Microsoft-s-requirements-for-email-senders). The owner can still receive inquiries at Gmail. Render's Docker build verifies PHP cURL. If credentials are missing, the form is unavailable and the direct email link remains visible.

## Configure localhost in XAMPP

Set the same four variables for Apache in a private config outside the website directory. On this machine the include is C:/xampp/apache/conf/extra/portfolio-brevo.local.conf. Use a private Brevo API key and the verified sender address, then restart Apache. Never commit the key to this repository.

## Verify delivery

1. Submit one labeled inquiry from the deployed form using an authorized visitor address.
2. In Brevo, open Transactional → Logs and filter by the recipients or the tags `portfolio-inquiry` and `portfolio-acknowledgment`. Check both delivery events.
3. Check both inboxes and their spam folders. Compare the owner subject, visitor reply address, submitted fields, and separate acknowledgment.
4. If sending fails, check Render Logs for entries starting with `Portfolio contact:`. They distinguish the owner and visitor calls and show HTTP status, cURL error number, or an accepted message ID. API acceptance and inbox delivery are separate results.

The site uses temporary contact-session data to validate form tokens and suppress duplicates. A failed acknowledgment does not cause PHP to resend the owner inquiry.
