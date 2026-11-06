# Christopher John Cruz — Personal Portfolio

A responsive personal portfolio built with **PHP, HTML, CSS, and JavaScript**, showcasing technical projects, UI/UX designs, certifications, and creative works. Features include interactive animations, light/dark themes, project galleries, and Brevo email integration.

**Live Website:** https://christopherjohncruz.onrender.com

## Technologies Used

- **Frontend:** HTML5, CSS3, JavaScript
- **Backend:** PHP
- **Email Service:** Brevo API
- **Web Server:** Apache (XAMPP)
- **Deployment:** Render (Docker)
- **Version Control:** Git and GitHub

## System Requirements

| Requirement | Version |
|---|---|
| PHP | 8.0 or higher |
| Local PHP | 8.2.12 (tested) |
| Production PHP | 8.4 (Docker) |
| XAMPP | PHP 8.0+ |
| PHP Extensions | cURL, OpenSSL |
| Database | Not required |

## How to Run Locally

### Option 1: Using XAMPP

1. Install [XAMPP](https://www.apachefriends.org/).
2. Place the project folder in:

   `C:\xampp\htdocs\CruzMidtermProjectPortfolio`

3. Open XAMPP Control Panel and start **Apache**.
4. Open your browser and visit:

   **http://localhost/CruzMidtermProjectPortfolio/**

### Option 2: Using PHP Terminal

1. Open PowerShell or VS Code Terminal.
2. Navigate to the project folder:

```powershell
cd C:\xampp\htdocs\CruzMidtermProjectPortfolio
```

3. Check your PHP version:

```powershell
& "C:\xampp\php\php.exe" -v
```

4. Start the PHP server:

```powershell
& "C:\xampp\php\php.exe" -S 127.0.0.1:8000 -t .
```

5. Visit http://127.0.0.1:8000/.

Press `Ctrl + C` to stop the server.

## Website Features

- **Home:** Animated introduction, interactive portrait, and theme switching.
- **About:** Education, expertise, credentials, materials, and tools.
- **Work Gallery:** 12 projects with filters, screenshots, and project details.
- **Creative Works:** Graphic designs and interactive image previews.
- **Contact:** Form validation and Brevo email notifications.
- **Legal Pages:** Privacy Policy, Terms of Service, and Licenses & Attribution.

## Project Structure

```text
CruzMidtermProjectPortfolio/
├── index.php
├── project.php
├── privacy.php
├── terms.php
├── licenses.php
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
├── config/
├── data/
├── includes/
├── pages/
├── docs/
├── tools/
├── Dockerfile
├── render.yaml
└── README.md
```

## Brevo Email Configuration

Configure these environment variables in Render:

```env
BREVO_API_KEY
BREVO_SENDER_EMAIL
BREVO_SENDER_NAME
CONTACT_OWNER_EMAIL
```

Keep API credentials private. See `docs/contactSetup.md` for instructions.

## Render Deployment

1. Push the project to GitHub.
2. Create a Web Service in [Render](https://dashboard.render.com/).
3. Connect the repository and select **Docker**.
4. Configure the Brevo environment variables.
5. Deploy and test the website.

See `docs/renderDeployment.md` for additional instructions.

## Testing

Run these commands in the project directory:

```powershell
& "C:\xampp\php\php.exe" tools/verifyPortfolio.php
& "C:\xampp\php\php.exe" tools/verifyContact.php
& "C:\xampp\php\php.exe" tools/verifyBrevoContact.php
& "C:\xampp\php\php.exe" tools/verifyLegal.php
```

Email delivery must be verified separately through Brevo and the recipient inboxes.

## Developer

**Christopher John Cruz**

BS Information Technology — Pamantasan ng Lungsod ng Valenzuela

**Portfolio:** https://christopherjohncruz.onrender.com

---

*Thoughtful design. Useful code. Always growing.*