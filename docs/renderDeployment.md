# Deploy the portfolio to Render

The portfolio runs on PHP and Apache in Docker. The image includes the site, its local assets, and the four policy routes. Contact messages go from the visitor’s browser to FormSubmit. Deployment needs no database, package install, or email credentials.

## Render setup

1. Push the portfolio files to the connected Git repository. In Render, choose **New → Blueprint** and select the branch with `render.yaml`.
2. Review the proposed Docker web service and create it. For manual setup, choose **New → Web Service**, connect the Git repository, select the Docker runtime and `./Dockerfile`, and use `/` for the health check. Leave the Docker command blank.
3. Wait for the service to go live and open the HTTPS URL Render assigns. Check the home page, each policy page, and a direct project route such as `project.php?project=vision-aid`. Verify that the PDF and video buttons open their files.
4. If the inbox has yet to be activated with FormSubmit, follow [contact setup](contactSetup.md) from the published site.

Apache serves the repository’s public PHP entry points and assets from `/var/www/html` and listens on `0.0.0.0:$PORT`. The default port is `10000`. Internal PHP templates and data are blocked over HTTP. Public links resolve relative to the current site, so the assigned Render address and a custom domain both work without changing the source.

## Local container check

```powershell
docker build -t cjc-portfolio .
docker run --rm -d --name cjc-portfolio -p 8080:10000 cjc-portfolio
powershell -NoProfile -File .\docker\verify-container.ps1 -BaseUrl http://127.0.0.1:8080
docker logs cjc-portfolio
docker stop cjc-portfolio
```

The verifier follows local assets, project links, and policy routes. It checks private paths and the form destination without sending a message. Check Render's build and runtime logs if the service fails its health check.

- [Render Docker deployments](https://render.com/docs/docker)
- [Render web services and ports](https://render.com/docs/web-services)
- [Render Blueprint reference](https://render.com/docs/blueprint-spec)
