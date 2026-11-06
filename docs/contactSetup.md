# Portfolio contact form

The contact form posts directly from the browser to [FormSubmit](https://formsubmit.co/) for delivery to the portfolio Gmail inbox. It sends the entered name, email, selected project brief, message, and required inquiry-processing acknowledgment. The form requests a confirmation email and leaves FormSubmit verification enabled. In a browser with session storage on a public HTTPS site, FormSubmit returns visitors to the contact section after accepting the form.

## Activate and test

1. Open the contact form on the published portfolio and send a clearly labeled test message.
2. Look for the FormSubmit activation email in the recipient inbox, including Spam. Follow its activation link if requested.
3. Send another test and verify that the inquiry arrives. Check the email address you entered for the requested confirmation.

No Gmail password or SMTP configuration is needed. If messages do not arrive, check the activation status and spam settings. The portfolio does not store messages in a database. [FormSubmit documentation](https://formsubmit.co/documentation) and [delivery and retention guidance](https://formsubmit.co/help) describe provider behavior.
