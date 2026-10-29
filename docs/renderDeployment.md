# Deploy the portfolio to Render with Docker

This is a plain PHP website, with PHP templates, local CSS/JavaScript/fonts, supplied project images, and a password-free FormSubmit contact relay. It has no Node build, Composer runtime dependency, or database requirement. The unused PHPMailer directory is excluded from the image.

The root `Dockerfile` uses the official `php:8.4-apache-bookworm` image. It selects PHP's production configuration, checks cURL/OpenSSL/session/JSON/OPcache, lints the application during the build, and starts Apache in the foreground. A separate build stage would add no benefit because there is no compiled frontend or dependency build to discard. Only runtime files are copied; `.dockerignore` excludes everything else by default.

## Local Docker verification

Install and start Docker Desktop with Linux containers, then open PowerShell in this repository:

```powershell
docker build --pull -t cjc-portfolio .
docker run --rm -d --name cjc-portfolio -p 8080:10000 -e PORT=10000 -e PORTFOLIO_CONTACT_ENABLED=false cjc-portfolio
docker logs cjc-portfolio
powershell -NoProfile -ExecutionPolicy Bypass -File .\docker\verify-container.ps1 -BaseUrl http://localhost:8080
docker inspect --format='{{.State.Health.Status}}' cjc-portfolio
```

Open `http://localhost:8080/` in a browser. Check both themes, the real-to-pixel portrait hover/tap, toolkit motion and its pause control, project selection and detail page refreshes, credential details/previews, and phone/tablet layouts. The HTTP verifier follows rendered local assets and project links, checks the safe 404 page, verifies the contact handler rejects missing CSRF tokens, and checks that private PHP folders and excluded files cannot be requested. It does not send email.

To confirm a different runtime port works:

```powershell
docker stop cjc-portfolio
docker run --rm -d --name cjc-portfolio -p 8080:12345 -e PORT=12345 -e PORTFOLIO_CONTACT_ENABLED=false cjc-portfolio
powershell -NoProfile -ExecutionPolicy Bypass -File .\docker\verify-container.ps1 -BaseUrl http://localhost:8080
docker stop cjc-portfolio
```

The current editing machine has no Docker or Podman executable installed. The image build, container startup, Apache configuration inside Linux, and container HTTP verifier therefore **have not been executed here**. They must pass on a Docker host or during Render's first build before calling the deployment verified. The files include build-time dependency/lint assertions and a container health check to surface failures early.

## Deploy through the Render dashboard

1. Review and push this repository, including `Dockerfile`, `.dockerignore`, `docker/`, and `render.yaml`, to your GitHub repository. Do not add passwords, `.env` files, SQL exports, or local mail configuration.
2. In Render, choose **New → Web Service → Git Provider**. Connect GitHub and select `ChrisinProgress/EDPMidtermPortfolio`.
3. Select the branch you want to deploy. Keep the repository root as the root directory, choose **Docker** as the language/runtime, and use `./Dockerfile` as the Dockerfile path. Leave the Docker Command blank so the image's startup command runs. A separate build command is unnecessary.
4. Choose a service name, region, and compute plan. The included Blueprint uses Singapore and the Free plan; choose a different plan if needed. Read Render's current Free plan limitations before relying on it for client inquiries.
5. Set the health check path to `/`. `PORT` defaults to `10000`; the startup script also honors an alternative Render `PORT` value. The listener binds to `0.0.0.0`, not a loopback-only address.
6. Keep `PORTFOLIO_CONTACT_ENABLED=true`. No Gmail password, SMTP server, or database variables are needed. The script derives `PORTFOLIO_SITE_URL` from Render's supplied `RENDER_EXTERNAL_URL`, including its HTTPS scheme. If using a custom domain, explicitly set `PORTFOLIO_SITE_URL=https://your-domain.example/` instead.
7. Enable automatic deploys on commits to the connected branch, then create the web service. Watch the build and runtime logs until Render reports **Live** and the health check passes.
8. Open the assigned HTTPS URL. Refresh a direct `project.php?project=...` link, navigate back to the portfolio sections, test certificate previews, and confirm all images/fonts load. Then send one clearly labeled contact test yourself and confirm its name, email, and message arrive in the recipient inbox. Check FormSubmit activation again for the new public website address if needed. A relay success response alone does not prove inbox delivery.

Use the connected **Git Provider** workflow for future push-triggered deployment. Render's public-repository-URL and prebuilt-image workflows do not provide the same Git-based automatic deployment behavior.

## Blueprint alternative

Instead of creating the web service manually, choose **New → Blueprint**, connect the repository and branch, and select its root `render.yaml`. Review the proposed service and plan before creating it. The Blueprint declares one Docker web service, `/` health checks, and commit-triggered automatic deploys. It deliberately omits `repo` and `branch` so Render uses the connected Blueprint repository and selected branch. It creates no database or persistent disk.

## Runtime behavior

| Setting | Behavior |
| --- | --- |
| `PORT` | Apache listener port, default `10000`; startup rejects values outside `1`–`65535`. |
| `PORTFOLIO_CONTACT_ENABLED` | `true` in Render; set `false` for tests or maintenance without outgoing mail. |
| `PORTFOLIO_SITE_URL` | Optional full public URL override, primarily for custom domains. Never set it to localhost in production. |
| `RENDER_EXTERNAL_URL` | Supplied by Render; used automatically as the contact relay Referer when no override is set. |

Render terminates HTTPS at its proxy. The container's Apache configuration maps forwarded HTTPS into PHP's existing HTTPS detection so contact session cookies use `Secure` on public HTTPS requests. Application templates, configuration, and data remain available to PHP but HTTP access to `config/`, `data/`, `includes/`, and `pages/` returns `403`. Documentation, tools, `.git`, and vendor files are absent from the image. Assets are served by Apache with compression and modest browser caching.

All navigation and asset references remain relative to the application, so deployment at the assigned public root URL does not need a `MidtermProjectPortfolio` path prefix or a hardcoded host. This is a PHP multi-page site, so no SPA rewrite rule is necessary. PHP sessions contain CSRF/cooldown state only and reset after a deploy or restart; visitors with an old contact form may need to refresh. Run one service instance unless session storage is deliberately redesigned. Client messages are not stored in a local database.

If deployment fails, check Render's build/runtime logs first. Missing extensions or PHP syntax failures stop the build. An invalid `PORT` stops startup. A missing local asset produces an HTTP verifier failure. Contact relay failures return an honest error and should be investigated through the public URL, outbound HTTPS availability, and FormSubmit activation. Keep errors logged rather than enabling PHP error display on the public site.

## Official references

- [Render: Docker deployments](https://render.com/docs/docker)
- [Render: web service ports, HTTPS, and Git integration](https://render.com/docs/web-services)
- [Render: Blueprint fields](https://render.com/docs/blueprint-spec)
- [Render: default environment variables](https://render.com/docs/environment-variables)
- [Official PHP image: Apache and production PHP configuration](https://hub.docker.com/_/php)

These references were checked while preparing the configuration. No Render account, service, or live deployment was changed by this work.
