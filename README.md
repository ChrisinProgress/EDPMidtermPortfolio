# Christopher John Cruz — Personal Portfolio

A responsive PHP portfolio with blue and violet accents, a custom CJC monogram, light and dark themes, and dedicated project detail pages. The theme button remembers the visitor's choice and transitions between photographic and pixel-art portraits. Built with HTML, CSS, JavaScript, and PHP, with password-free FormSubmit forwarding for contact messages. No framework, database, package installation, or build step is required.

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

## Files and responsibilities

| File or folder | Purpose |
| --- | --- |
| `index.php` | Main portfolio, shared data, and hero portraits |
| `project.php` | Individual project details, labeled screenshot selectors, and repository links |
| `pages/` | Projects, About Me, Credentials, and Contact sections |
| `includes/` | Shared header, footer, SVG icon helper, toolkit, and reusable project card |
| `includes/brandLogo.php` | Custom CJC brand mark used in the header and footer |
| `includes/themePortrait.php` | Shared photograph/pixel-art portrait component |
| `includes/education.php` | Chronological education progress timeline |
| `includes/imageViewer.php` | Shared full-size image dialog and thumbnail selector |
| `data/projectData.php` | All 11 projects, descriptions, tools, and gallery image paths |
| `data/credentialsData.php` | Certificate titles, issuers, dates, descriptions, and image details |
| `data/toolkitData.php` | Programming, Design, Databases, and Hosting tools and local icons |
| `assets/css/style.css` | Layout, components, responsive breakpoints, and reduced-motion behavior |
| `assets/css/fonts.css` | Local Poppins font faces used throughout the website |
| `assets/fonts/poppins/` | Bundled Poppins weights and SIL Open Font License |
| `assets/css/refinements.css` | Readability and progressive-enhancement styles |
| `assets/css/theme.css` | Theme toggle and dark-mode colors for the entire page |
| `assets/css/credentials.css` | Compact responsive credential grid and issuer filter styling |
| `assets/js/main.js` | Navigation, project filters, screenshot selection, full-size viewer, and form feedback |
| `assets/js/credentialCollection.js` | Credential issuer filtering and visible count |
| `assets/js/theme.js` | Early theme initialization, accessible switching, and saved preference |
| `assets/css/toolkit.css` | Category tabs and the selected tool grid |
| `assets/js/toolkitTabs.js` | Toolkit category selection and keyboard tab navigation |
| `assets/css/work-gallery.css` | Static project cover grid and category filters |
| `assets/css/project-detail.css` | Project detail layout, screenshot stage, and thumbnail buttons |
| `assets/css/education-socials.css` | Social footer and shared education styling |
| `assets/css/education-timeline.css` | Completed/current education line, school cards, and responsive layout |
| `assets/css/identity.css` | Custom brand mark and theme portrait presentation |
| `assets/css/motion.css` | Animated ambient backgrounds, theme transitions, and interaction states |
| `assets/js/motion.js` | Progressive scroll reveals and reading progress |
| `assets/icons/toolkit/` | Local Devicon brand SVGs, license, and source attribution |
| `assets/icons/brand/` | Custom CJC monogram assets and favicon |
| `assets/images/projects/` | 51 supplied screenshots and design images across all 11 project galleries; no active placeholders |
| `assets/images/credentials/` | Eight supplied Canva, TESDA, and PLV certificate images |
| `assets/images/profile/` | Original hero photograph, campus photograph, and their pixel-art versions |
| `assets/images/education/` | Supplied PLV and Malinta National High School seals |
| `config/appConfig.php` | Session, escaping, CSRF, and the fixed contact relay destination |
| `includes/contactMailer.php` | HTTPS forwarding to FormSubmit with a fixed recipient |
| `handlers/contactHandler.php` | Validated POST endpoint that emails submissions and returns JSON or ordinary HTML |
| `docs/contactSetup.md` | FormSubmit activation, forwarding behavior, privacy, and troubleshooting |
| `tools/verifyPortfolio.php` | CLI data, asset, and reference checks |
| `tools/verifyContact.php` | Relay payload/response and disabled local HTTP checks that send no outgoing messages |

## Creative toolkit

The 16 tools are organized into Programming, Design, Databases, and Hosting tabs, with one category displayed at a time and Programming selected initially. Use the tabs or keyboard arrows/Home/End to change categories. Databases includes MySQL, Microsoft SQL Server, and Oracle; Hosting includes GitHub, Render, and Vercel. Local brand icons, readable labels, and hover/focus states support both themes. There is no carousel; without JavaScript, all four groups remain visible.

Update `data/toolkitData.php` to change the labels or icon paths. The bundled icons' release, source links, and license are recorded in `assets/icons/toolkit/SOURCES.md` and `LICENSE.txt`.

## Work gallery and interactions

The Work Gallery presents a static, responsive grid of project covers, with All, Technical, and Digital Design filters. Each card has one **View Project** link to `project.php?project=id`. Its detail page shows the description, technologies, and labeled screenshot thumbnails. Select a thumbnail to change the displayed image, then click the image to open the shared full-size viewer. The viewer also has labeled thumbnail choices, keyboard image navigation, and a close button; closing restores focus. No visible arrow controls are used.

The custom CJC mark appears in the header, footer, and favicon. The hero uses the original photograph in light mode and its new pixel-art version in dark mode; About uses the new campus photograph and its pixel-art version. These portraits share consistent frames and accessible descriptions.

The About education line runs chronologically from completed STEM studies at Malinta National High School (2022–2024) to the ongoing BSIT degree at Pamantasan ng Lungsod ng Valenzuela (2024–Present). Both supplied school seals appear beside the course details, and the current node has a subtle pulse. The icon-only footer links to the supplied Facebook and Instagram profiles, plus existing GitHub, LinkedIn, and email destinations, with accessible labels and keyboard focus states.

Both themes have ambient background motion, entrance reveals, hover/focus feedback, reading progress, and theme-switch transitions. Reduced-motion preferences disable decorative animation while preserving navigation and controls.

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

Portrait files live in `assets/images/profile/`. Configure the separate hero and About image sources in `index.php`; `pages/about.php` renders the About pair. The shared `renderThemePortrait()` component in `includes/themePortrait.php` pairs the two theme versions with one accessible description.

Project cards use the same local logos as the toolkit. Technology names remain available as image alternatives and hover labels; `Adobe Photoshop` uses the Photoshop icon.

Technical projects use their `repositoryUrl` in `data/projectData.php` for the labeled GitHub link on the detail page. Each repository opens in a new tab. Project links and original-image links remain usable without JavaScript.

No repository or live-demo URLs are invented. The supplied GitHub and LinkedIn profile links appear in Contact.

## Credentials

The Credentials section contains eight certificates, ordered with the latest completions first. Six are from Canva Design School: Marketing with Canva, completed on October 23, 2026 with one hour of training, and five courses completed on October 22, 2026: Canva Essentials, Graphic Design Essentials, AI Skills for Students, AI Essentials 2026, and Work Smarter with AI. The section also includes TESDA's Microsoft Digital Literacy course completion (June 4, 2025) and PLV's Identity & Access Management seminar participation (April 24, 2025).

Compact Poppins cards show the certificate thumbnail, title, issuer, date, and a short description in a responsive grid. All, Canva, and Other issuers filters update the visible cards and count. Cards respond to hover and keyboard focus. The section has no carousel; without JavaScript, all eight cards remain visible.

Click a certificate image or **View certificate** to open the original-resolution image in the shared viewer. Use its labeled thumbnails or keyboard image navigation to browse certificates; there are no visible arrow buttons. The close button or Escape closes the viewer and restores focus to the opening link. Without JavaScript, these links open the original images directly. Certificate images remain separate from the 11 projects and 51 project gallery images. Update `data/credentialsData.php` to maintain their content and descriptive local image filenames.

## Contact messages

Valid contact submissions forward the visitor's name, email address, and message to `cruchristopherjohnhoylar@gmail.com` through FormSubmit. The recipient is fixed on the server; the visitor's email field enables replies to the client. A successful HTTP 200 response means the relay accepted the submission; it does not prove inbox arrival. Disabled forwarding returns HTTP 503, and relay rejection or transport failure returns HTTP 502 without claiming success.

The application does not store contact messages in a database. The `midterm_portfolio` database and its project connection, schema, and setup files were removed; other databases were left unchanged. No Gmail password or mail credential is used. FormSubmit requires a one-time email activation after the first submission. It processes the submitted details and documents a 30-day submission archive; see [contact setup](docs/contactSetup.md) for activation and privacy details.

The endpoint checks POST, CSRF, required fields, email format, field lengths, control characters, and a honeypot. The client prevents duplicate in-flight submissions; successfully sent messages have a 60-second session cooldown and a ten-minute duplicate check. Delivery errors do not start the cooldown. The handler also works without JavaScript. PHP sessions need a writable configured session directory.

## Verification

```powershell
& 'C:\xampp\php\php.exe' tools/verifyPortfolio.php
Get-ChildItem -Recurse -Filter '*.php' | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
node --check assets/js/main.js
node --check assets/js/credentialCollection.js
node --check assets/js/toolkitTabs.js
node --check assets/js/theme.js
node --check assets/js/motion.js
```

The portfolio check covers 11 projects (6 technical and 5 digital design), their 51 supplied gallery images, and eight credentials. It checks data completeness, image paths and dimensions, alternative text, unique IDs and files, imports, SVG validity, and navigation fragments. Browser verification should cover project filters and detail links, labeled screenshot selection, full-size viewing and focus restoration, toolkit tabs, credential issuer filters, theme portraits, mobile navigation, form feedback, and desktop/tablet/mobile layouts. `tools/verifyContact.php` provides relay payload/response and disabled local HTTP checks without sending real email. A live forwarding test requires the owner to complete FormSubmit's email activation.

Navigation, project detail links, original-image links, and the contact form remain usable without JavaScript. All toolkit groups and credentials remain visible; enhanced filter and selection controls are hidden. Smooth transitions respect the visitor's reduced-motion preference.
