# Web Developer Portfolio + Admin Panel

A complete portfolio website for a web developer with a full admin dashboard, built for **XAMPP** (Apache + PHP + MySQL).

## Technologies used

| Layer | Technologies |
|-------|--------------|
| Frontend | HTML5, CSS3, JavaScript, Bootstrap 5 |
| Backend | PHP 8, MySQL (MariaDB) |
| Framework | Angular.js (admin dashboard) |
| Tooling | TypeScript (compiled to JS in `admin/assets/js/app.js`) |
| Skills showcased | WordPress, Mobirise |

## Features

### Public website (`/portfolio/index.php`)
- Hero with animated typewriter effect
- About section (photo, contact info)
- Skills with animated progress bars (grouped by category)
- Services grid
- Resume / Experience timeline
- Filterable projects portfolio
- Contact form (saved to MySQL)
- Fully responsive, dark modern design

### Admin panel (`/portfolio/admin/`)
- Secure login (bcrypt passwords, sessions)
- Dashboard with stats, charts and recent messages (powered by **Angular.js** reading a PHP JSON API)
- **Projects** CRUD with image upload
- **Services** CRUD
- **Skills** CRUD
- **Messages** inbox (read/unread/delete/reply)
- **Settings**: site content, hero, about, resume, contact info, social links and password change

## Installation (XAMPP)

1. Copy this `portfolio` folder into `C:\xampp\htdocs\` (or `D:\xampp\htdocs\`).
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Import the database:
   - Open http://localhost/phpmyadmin
   - Import `database.sql`  (or run it from the MySQL CLI)
4. Open the site:
   - Portfolio: http://localhost/portfolio/
   - Admin: http://localhost/portfolio/admin/

### Default admin login
```
Username: admin
Password: admin123
```
Change the password from **Settings → Password** after logging in.

### Database configuration
Edit `includes/config.php` if your MySQL credentials differ from XAMPP defaults:
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'portfolio_db');
```

## Project structure

```
portfolio/
├── database.sql            # Database + seed data
├── index.php               # Public portfolio site
├── includes/
│   ├── config.php          # DB connection
│   ├── functions.php       # Helpers (settings, uploads, queries)
│   ├── header.php          # Site header + navbar
│   └── footer.php          # Site footer
├── assets/                 # Public CSS/JS
│   ├── css/style.css
│   └── js/main.js
├── api/
│   └── stats.php           # JSON API for the Angular.js dashboard
└── admin/
    ├── index.php           # Login
    ├── dashboard.php       # Angular.js dashboard
    ├── projects.php        # Projects CRUD
    ├── services.php        # Services CRUD
    ├── skills.php          # Skills CRUD
    ├── messages.php        # Inbox
    ├── settings.php        # Site settings + password
    ├── includes/           # auth guard + admin layout
    ├── uploads/            # Uploaded images
    └── assets/
        ├── css/admin.css
        ├── js/app.js       # Compiled JavaScript
        └── ts/app.ts       # TypeScript source
```

## Notes
- Uploaded images are stored in `admin/uploads/` (PHP execution is blocked there via `.htaccess`).
- The admin TypeScript source is at `admin/assets/ts/app.ts`; it is compiled to `admin/assets/js/app.js` and the compiled file is committed, so no build step is required.
