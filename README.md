# Christopher John Cruz — Personal Portfolio

A responsive PHP portfolio with blue and violet accents, a bold circular black-and-white CJC monogram, light and dark themes, and dedicated project detail pages. The theme button remembers the visitor's choice. The hero uses a transparent portrait cutout; both portraits show the photograph in either theme and change to pixel art on hover or interaction. Built with HTML, CSS, JavaScript, and PHP, with password-free FormSubmit forwarding for contact messages. No framework, database, package installation, or frontend build step is required.

## Run with XAMPP

1. Keep this folder at `C:\xampp\htdocs\MidtermProjectPortfolio`.
2. Start **Apache** in the XAMPP Control Panel.
3. Open [the portfolio](http://localhost/MidtermProjectPortfolio/).
4. Submit the contact form once, then check `cruchristopherjohnhoylar@gmail.com` for a FormSubmit activation email. Click its activation link and send another test message to confirm forwarding. Check Spam if needed. No Gmail password is required. See [contact setup](docs/contactSetup.md).

Alternatively, run this command from the project folder in PowerShell:

```powershell
& 'C:\xampp\php\php.exe' -S 127.0.0.1:8000 -t .
```

Then open [the PHP preview](http://127.0.0.1:8000/). Press Ctrl+C to stop the PHP preview. Use a PHP server rather than opening files directly or using a static-only preview server. PHP 8.0 or newer with cURL, OpenSSL, and an internet connection is required for contact forwarding; this project was checked with XAMPP PHP 8.2.12. MySQL is not needed.

## Deploy on Render with Docker

The repository includes a PHP 8.4 Apache `Dockerfile`, an allowlisted `.dockerignore`, production PHP/Apache settings, and a `render.yaml` Blueprint. The startup script binds Apache to `0.0.0.0` using Render's `PORT`. Render's assigned HTTPS URL is used automatically by the contact relay, with an optional custom-domain override.

Push the updated repository to GitHub, then create a Render **Web Service**, connect the repository, and choose **Docker** as the runtime. Keep the Dockerfile and build context at the repository root. Alternatively, create a Blueprint using `render.yaml`. Connected-branch commits can trigger automatic redeployment. See [the deployment guide](docs/renderDeployment.md) for exact dashboard steps, environment variables, local container commands, and the HTTP verifier.

Docker is not installed in the current workspace environment, so the image build and container startup have **not** been verified here. PHP, shell, and configuration checks passed; run the guide's container checks on a Docker-enabled machine or verify Render's first build and runtime logs before treating deployment as complete.

## Files and responsibilities

| File or folder | Purpose |
| --- | --- |
| `index.php` | Main portfolio, shared data, and hero portraits |
| `project.php` | Individual project details, labeled screenshot selectors, and repository links |
| `pages/` | Projects, About Me, Credentials, and Contact sections |
| `includes/` | Shared header, footer, SVG icon helper, toolkit, and reusable project card |
| `includes/brandLogo.php` | Custom CJC brand mark used in the header and footer |
| `includes/themePortrait.php` | Shared photograph/pixel-art portrait component |
| `includes/education.php` | Integrated About studies line, with PLV first and Malinta second |
| `includes/imageViewer.php` | Shared full-size image dialog and thumbnail selector |
| `data/projectData.php` | All 11 projects, descriptions, tools, and gallery image paths |
| `data/credentialsData.php` | Certificate titles, issuers, dates, descriptions, and image details |
| `data/toolkitData.php` | Development, Design, Databases, and Hosting tools, short descriptions, and local icons |
| `assets/css/style.css` | Layout, components, responsive breakpoints, and reduced-motion behavior |
| `assets/css/fonts.css` | Local Poppins font faces used throughout the website |
| `assets/fonts/poppins/` | Bundled Poppins weights and SIL Open Font License |
| `assets/css/refinements.css` | Readability and progressive-enhancement styles |
| `assets/css/theme.css` | Theme toggle and dark-mode colors for the entire page |
| `assets/css/credentials.css` | Alternating center-timeline gallery, glow, and certificate details dialog |
| `assets/js/main.js` | Navigation, project filters, screenshot selection, full-size viewer, and form feedback |
| `assets/js/credentialCollection.js` | Progressive certificate-gallery interactions |
| `assets/js/theme.js` | Early theme initialization, accessible switching, and saved preference |
| `assets/css/toolkit.css` | One continuous horizontal toolkit ribbon |
| `assets/js/toolkitTabs.js` | Pausable toolkit motion and keyboard/touch scrolling |
| `assets/css/work-gallery.css` | Layered project previews, flip details, and category filters |
| `assets/js/workGallery.js` | Project dropdown, neighboring previews, keyboard browsing, and flip controls |
| `assets/css/project-detail.css` | Project detail layout, screenshot stage, and thumbnail buttons |
| `assets/css/education-socials.css` | Social footer and shared education styling |
| `assets/css/education-timeline.css` | Animated About studies line, current/completed nodes, and responsive layout |
| `assets/css/identity.css` | Circular brand mark and interactive real/pixel portrait transitions |
| `assets/css/creative-hero.css` | Background-free hero portrait, floating aura, and subtle motion |
| `assets/css/interests.css` | Six personal hobby icons and playful hover movements |
| `assets/js/portraitInteraction.js` | Keyboard and touch controls for the portrait effect |
| `assets/css/motion.css` | Animated ambient backgrounds, theme transitions, and interaction states |
| `assets/js/motion.js` | Progressive scroll reveals and reading progress |
| `assets/icons/toolkit/` | Local Devicon brand SVGs, license, and source attribution |
| `assets/icons/brand/` | Custom CJC monogram assets and favicon |
| `assets/images/projects/` | 51 supplied screenshots and design images across all 11 project galleries; no active placeholders |
| `assets/images/credentials/` | Eight supplied Canva, TESDA, and PLV certificate images |
| `assets/images/profile/` | Original photographs, pixel versions, and transparent hero cutouts |
| `assets/images/education/` | Supplied PLV and Malinta National High School seals |
| `config/appConfig.php` | Session, escaping, CSRF, and the fixed contact relay destination |
| `includes/contactMailer.php` | HTTPS forwarding to FormSubmit with a fixed recipient |
| `handlers/contactHandler.php` | Validated POST endpoint that emails submissions and returns JSON or ordinary HTML |
| `docs/contactSetup.md` | FormSubmit activation, forwarding behavior, privacy, and troubleshooting |
| `tools/verifyPortfolio.php` | CLI data, asset, and reference checks |
| `tools/verifyContact.php` | Relay payload/response and disabled local HTTP checks that send no outgoing messages |
| `Dockerfile`, `render.yaml`, `docker/` | Production container, Render Blueprint, startup settings, and deployment checks |

## Creative toolkit

All 16 tools share one continuous horizontal row of logos, names, and short descriptions, without category headings, sorting controls, or category buttons. It includes development and design tools, MySQL, Microsoft SQL Server, Oracle, GitHub, Render, and Vercel.

The row has no arrow controls. Hovering pauses its motion; keyboard focus or touch switches to native horizontal scrolling. A Pause/Play motion button gives explicit control. Motion also pauses when the row is offscreen or the page is hidden. Reduced-motion preferences use a manually scrollable row instead. Without JavaScript, all tools remain available in the same scrollable row. Repeated animation copies are hidden from assistive technology.

Update `data/toolkitData.php` to change the labels or icon paths. The bundled icons' release, source links, and license are recorded in `assets/icons/toolkit/SOURCES.md` and `LICENSE.txt`.

## Work gallery and interactions

The Work Gallery presents a featured project with up to four layered neighboring previews, plus All, Technical, and Digital Design filters. Choose another project from the dropdown, select a neighboring preview, or use keyboard navigation on the gallery stage. Hover or activate the featured cover to reveal its back, where **View Project** opens `project.php?project=id`. The gallery uses no visible arrow controls.

Each detail page shows the project description, technologies, and labeled screenshot thumbnails. Select a thumbnail to change the displayed image, then click the image to open the shared full-size viewer. The viewer also has labeled thumbnail choices, keyboard image navigation, and a close button; closing restores focus.

The bold circular CJC line monogram appears in the header, footer, favicon, introduction, and About design. It is black in light mode and white in dark mode. A faint animated copy sits behind the About text. Both themes show the real photograph at rest: the hero uses a background-free cutout, while About uses the campus photograph. Hovering changes either image to pixel art; removing the pointer restores the photograph. Keyboard and touch controls also toggle the effect. The hero's aura and gentle floating motion blend it into the interface. Original images are retained; [the cutout asset record](docs/visualAssets.md) contains the saved paths and edit prompts.

The interests area adds illustrated Gaming, Anime, Biking, Board games, Traveling, and Editing cards. Each icon has a small individual hover movement, with reduced-motion support.

Studies are integrated within About, without a separate Education heading. The horizontal line places Pamantasan ng Lungsod ng Valenzuela first, with the ongoing BSIT degree (2024–Present), followed by completed STEM studies at Malinta National High School (2022–2024). Both supplied school seals appear beside the course details. The line animates subtly and the current node pulses; it adapts vertically on narrow phones. The icon-only footer links to the supplied Facebook and Instagram profiles, plus existing GitHub, LinkedIn, and email destinations, with accessible labels and keyboard focus states.

Both themes have ambient background motion, entrance reveals, hover/focus feedback, reading progress, and theme-switch transitions. Reduced-motion preferences disable decorative animation while preserving navigation and controls.

The visible FormSubmit forwarding sentence is omitted from the contact form. This presentation change does not alter the forwarding endpoint or the setup and privacy details documented below.

## Project gallery images

All 11 project galleries use supplied PNG screenshots and design images:

- Recipe Management System: Main Menu, Create Recipe, Saved Recipes, Pantry, and Shopping List.
- Group Portfolio: Registration, Team Profiles, About Me, Educational Background, and Contact Information.
- Airplane Passenger Queue System: Passenger Registration, Seating Arrangement, Queue Display, and Boarding Status.
- Tricycle Route Navigator: Route Selection, Shortest Route, Graph Map Layout, and Path Details.
- Dental Clinic Management System: Landing Page, Dental Services, Account Registration, Patient Dashboard, Admin Dashboard, Dentist Dashboard, Salary and Transactions, and Receptionist Dashboard.
- Dental Clinic Booking System: Landing Page, Sign In, Patient Dashboard, Appointment Request, Treatment Summary, and Profile Settings.
- AlgoVerse: Project Overview, Mobile UI Design, Style Guide, and Site Map.
- SaveMore Digital Bank: Project Overview, Mobile UI Design, Site Map, Logo Design, Style Guide, and UI Components.
- Artist Profile: Final Poster, Portrait Detail, and Photoshop Layout.
- Social Media Advertisement: Campaign Mockup, Photoshop Layout, and Canva Poster Design.
- Cozy Grind Brand Identity: Brand Board, Logo Design, and Canva Layout.

These 51 supplied images use descriptive filenames identifying the project and screen. AlgoVerse uses `algoverse-learning-app-project-overview.png`, `algoverse-learning-app-mobile-ui-design.png`, `algoverse-learning-app-style-guide.png`, and `algoverse-learning-app-site-map.png`. SaveMore uses `savemore-digital-bank-project-overview.png`, `savemore-digital-bank-mobile-ui-design.png`, `savemore-digital-bank-site-map.png`, `savemore-digital-bank-logo-design.png`, `savemore-digital-bank-style-guide.png`, and `savemore-digital-bank-ui-components.png`. Artist Profile uses `artist-profile-final-poster.png`, `artist-profile-photoshop-portrait-detail.png`, and `artist-profile-photoshop-full-layout.png`. Social Media Advertisement uses `social-media-advertisement-mango-campaign-mockup.png`, `social-media-advertisement-photoshop-layout.png`, and `social-media-advertisement-canva-poster-design.png`. Cozy Grind uses `cozy-grind-brand-identity-brand-board.png`, `cozy-grind-brand-identity-photoshop-logo-design.png`, and `cozy-grind-brand-identity-canva-brand-layout.png`. The original SVG placeholder assets remain in the assets folder but are no longer used in any project gallery.

For project screenshots, add local JPG, PNG, or WebP files and update each project's `images` entries in `data/projectData.php`. Each image has a `src`, descriptive `alt`, and short `label`; set `isPlaceholder` to `false` for actual screenshots. Omitted flags default to placeholders, which must retain their disclosure in both the image and alternative text. The detail page's thumbnail collection adapts to the number of entries. All displayed counts and categories are calculated from the central data.

Portrait files live in `assets/images/profile/`. Configure the separate hero and About image sources in `index.php`; `pages/about.php` renders the About pair. The shared `renderThemePortrait()` component in `includes/themePortrait.php` pairs the photographic and pixel-art versions independently of the selected theme.

Project cards use the same local logos as the toolkit. Technology names remain available as image alternatives and hover labels; `Adobe Photoshop` uses the Photoshop icon.

Technical projects use their `repositoryUrl` in `data/projectData.php` for the labeled GitHub link on the detail page. Each repository opens in a new tab. Project links and original-image links remain usable without JavaScript.

No repository or live-demo URLs are invented. The supplied GitHub and LinkedIn profile links appear in Contact.

## Credentials

The Credentials section contains eight certificates, ordered with the latest completions first. Six are from Canva Design School: Marketing with Canva, completed on October 23, 2026 with one hour of training, and five courses completed on October 22, 2026: Canva Essentials, Graphic Design Essentials, AI Skills for Students, AI Essentials 2026, and Work Smarter with AI. The section also includes TESDA's Microsoft Digital Literacy course completion (June 4, 2025) and PLV's Identity & Access Management seminar participation (April 24, 2025).

All eight certificates alternate left and right of a connected center timeline. Each compact card shows the certificate image, title, issuer, date, and a short description. Numbered nodes, glowing connectors, and entrance/hover/focus effects distinguish this collection from the Work Gallery. On narrow screens the rail moves left and cards stack in reading order. There are no issuer filters or buttons over the certificate pictures. Reduced-motion preferences disable decorative motion.

Select **View Details** to open a dialog with the issuing organization, date, type, description, and certificate preview. Close or Escape returns focus to the original control. Without JavaScript, native expandable details provide the same information. Click a certificate image to open the original-resolution image in the shared viewer, with labeled thumbnails and keyboard navigation, without visible arrow buttons. Certificate images remain separate from the 11 projects and 51 project gallery images. Update `data/credentialsData.php` to maintain their content and descriptive local image filenames.

## Contact messages

Valid contact submissions forward the visitor's name, email address, and message to `cruchristopherjohnhoylar@gmail.com` through FormSubmit. The recipient is fixed on the server; the visitor's email field enables replies to the client. A successful HTTP 200 response means the relay accepted the submission; it does not prove inbox arrival. Disabled forwarding returns HTTP 503, and relay rejection or transport failure returns HTTP 502 without claiming success.

The application does not store contact messages in a database. The `midterm_portfolio` database and its project connection, schema, and setup files were removed; other databases were left unchanged. No Gmail password or mail credential is used. FormSubmit requires a one-time email activation after the first submission. It processes the submitted details and documents a 30-day submission archive; see [contact setup](docs/contactSetup.md) for activation and privacy details.

The endpoint checks POST, CSRF, required fields, email format, field lengths, control characters, and a honeypot. The client prevents duplicate in-flight submissions; successfully sent messages have a 60-second session cooldown and a ten-minute duplicate check. Delivery errors do not start the cooldown. The handler also works without JavaScript. PHP sessions need a writable configured session directory.

## Verification

```powershell
& 'C:\xampp\php\php.exe' tools/verifyPortfolio.php
Get-ChildItem -Recurse -Filter '*.php' | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
node --check assets/js/main.js
node --check assets/js/workGallery.js
node --check assets/js/credentialCollection.js
node --check assets/js/toolkitTabs.js
node --check assets/js/portraitInteraction.js
node --check assets/js/theme.js
node --check assets/js/motion.js
```

The portfolio check covers 11 projects (6 technical and 5 digital design), their 51 supplied gallery images, and eight credentials. It checks data completeness, image paths and dimensions, alternative text, unique IDs and files, imports, SVG validity, and navigation fragments. Browser verification should cover project selection and detail links, full-size viewing and focus restoration, toolkit motion and scrolling, the centered certificate timeline and details dialog, hobby icons, portrait interaction in both themes, mobile navigation, form feedback, and desktop/tablet/mobile layouts. `tools/verifyContact.php` provides relay payload/response and disabled local HTTP checks without sending real email. A live forwarding test requires the owner to complete FormSubmit's email activation.

Navigation, project detail links, original-image links, and the contact form remain usable without JavaScript. All tools and credentials remain available; enhanced filter and selection controls are hidden. Smooth transitions respect the visitor's reduced-motion preference.
