# Deploy /Namsa Florals to cPanel Shared Hosting

Step-by-step guide to host this Laravel app on **cPanel** (or similar) **shared hosting** so **namsa.com.na** serves the site.

---

## Recommended: Drop into public_html (no document-root change)

**Everything is pre-built.** You upload the package into **public_html** and you’re done. No separate `public` folder, no changing document root.

### 1. Build the package (on your computer)

- **.env** must have **MySQL** configured (DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD).  
- In the project folder, run:
  ```bash
  php build-deploy.php
  ```
- This creates **`public_html_ready/`** with the app, vendor, uploads, `.env` (from your local one), and **`database/dump.sql`**.

### 2. Edit `public_html_ready/.env` for the **host**

- Set **DB_DATABASE**, **DB_USERNAME**, **DB_PASSWORD**, **DB_HOST** (usually `localhost`) to your **cPanel MySQL** database and user.  
- Set **MAIL_PASSWORD** if you use SMTP for the contact form.  
- **APP_URL** = `https://namsa.com.na`.

### 3. On the host (cPanel)

- **MySQL**: Create database and user, add user to database (All Privileges).  
- **phpMyAdmin** → your new database → **Import** → choose **`database/dump.sql`** from the package → Go.

### 4. Upload

- Upload **all contents** of **`public_html_ready/`** into **public_html** (File Manager or FTP).  
- The **root** of public_html must contain **`index.php`** and **`.htaccess`** (i.e. upload the *contents* of the folder, not the folder itself).

### 5. Permissions

- **storage** (recursive) → **775**  
- **bootstrap/cache** → **775**  
- **uploads** (and **uploads/products**, **uploads/logos**) → **775**

### 6. Done

- Visit **https://namsa.com.na**  
- Admin: **https://namsa.com.na/{your `ADMIN_PATH`}/login** (email: `admin@namsa.com.na`, password: shown once in the
  terminal when `AdminUserSeeder` runs).
- Log in and set up two-factor authentication immediately — it's required before the rest of the panel is reachable.

A **`README_DEPLOY.txt`** inside **`public_html_ready/`** repeats these steps.

---

## Alternative: Standard Laravel layout (document root = `public`)

If you prefer the usual Laravel structure and can set the document root to a subfolder:

### At a glance

1. **PHP 8.2+** + MySQL DB in cPanel  
2. **Local**: `composer install --no-dev`, create production `.env`  
3. **Upload** full project (including `vendor`) via SFTP / File Manager  
4. **Document root** → `yourfolder/public`  
5. **Permissions**: `storage` & `bootstrap/cache` writable (`775`)  
6. **SSH/Terminal**: `migrate` → `db:seed` → `storage:link` → `config:cache` (etc.)  
7. **SSL** on namsa.com.na, `APP_URL=https://namsa.com.na`

---

## Before You Start

- **PHP**: 8.2+ (Laravel 12). In cPanel → **Select PHP Version** / **MultiPHP INI Editor**.
- **Extensions**: `ctype`, `curl`, `dom`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `zip`. Enable any missing ones.
- **Domain**: `namsa.com.na` pointed to your hosting (A record or nameservers).
- **Upload size for product/gallery photos**: `.htaccess` already raises `upload_max_filesize`/`post_max_size` to 25M/30M, but hosts running PHP-FPM ignore `php_value` in `.htaccess`. If large photo uploads still fail, go to cPanel → **MultiPHP INI Editor** → select the domain → raise `upload_max_filesize` and `post_max_size` there directly (25M+ recommended).

---

## 1. Create MySQL Database (cPanel)

1. **cPanel** → **MySQL® Databases**.
2. **Create Database**: e.g. `youruser_namsaflora` (prefix often added by host).
3. **Create User**: e.g. `youruser_namsa` with a **strong password**. Save it.
4. **Add User to Database**: add the user to the new DB with **All Privileges**.
5. Note: **DB name**, **username**, **password**, **host** (often `localhost`).

---

## 2. Prepare the Project on Your Computer

Open a terminal in the project folder (e.g. `C:\xampp\htdocs\namsa flora`).

### 2.1 Install production dependencies

```bash
composer install --optimize-autoloader --no-dev
```

### 2.2 Create production `.env`

Copy your `.env` and adjust for production (or create from `.env.example`):

```env
APP_NAME="Namsa Flora"
APP_ENV=production
APP_KEY=base64:xxxx    # keep existing or run: php artisan key:generate
APP_DEBUG=false
APP_URL=https://namsa.com.na

# Database (use the MySQL DB you created)
DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=youruser_namsaflora
DB_USERNAME=youruser_namsa
DB_PASSWORD=your_db_password

SESSION_DRIVER=database
CACHE_STORE=database

# Mail (SMTP for contact form)
MAIL_MAILER=smtp
MAIL_HOST=mail.namsa.com.na
MAIL_PORT=587
MAIL_USERNAME=info@namsa.com.na
MAIL_PASSWORD=your-smtp-password   # use your real SMTP password on the server
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@namsa.com.na"
MAIL_FROM_NAME="/Namsa Florals"

# Contact & WhatsApp
CONTACT_PHONE=+264815574680
CONTACT_EMAIL_INFO=info@namsa.com.na
CONTACT_EMAIL_ORDERS=order@namsa.com.na
WHATSAPP_PAYMENT_NUMBER=264815574680
SOCIAL_WHATSAPP=https://wa.me/264815574680

# Social (namsa.florals)
SOCIAL_FACEBOOK=https://www.facebook.com/namsa.florals
SOCIAL_INSTAGRAM=https://www.instagram.com/namsa.florals
SOCIAL_TIKTOK=https://www.tiktok.com/@namsa.florals
```

**Important**: Never commit `.env` or share it. Use **SFTP** or **File Manager** to upload it; do **not** put it in Git.

### 2.3 Generate `APP_KEY` if needed

```bash
php artisan key:generate
```

Copy the new `APP_KEY` into your production `.env`.

---

## 3. Upload Files to the Server

### Option A: Document root = `public` (recommended)

Most cPanel setups let you set the **document root** for your domain to a folder like `public_html/namsa/public` or `namsaflora/public`.

1. Create a folder **outside** `public_html` (e.g. `namsaflora`) or inside (e.g. `public_html/namsaflora`).
2. Upload the **entire project** there (all folders: `app`, `bootstrap`, `config`, `database`, `public`, `resources`, `routes`, `storage`, `vendor`, plus `artisan`, `composer.json`, `composer.lock`).
3. **Do not** upload: `node_modules`, `.env.backup`, `.git`, `tests`, `*.md` (optional). **Do** upload your production `.env`.
4. If you have **product images or a custom logo** locally, upload `storage/app/public` contents (e.g. `storage/app/public/products`, `storage/app/public/logos`) so they exist on the server.
5. In **cPanel** → **Domains** → **Domains** (or **Addon Domains**):
   - Edit **namsa.com.na**.
   - Set **Document Root** to: `namsaflora/public` (or the full path like `/home/youruser/namsaflora/public`).
6. Ensure `public/.htaccess` is present (Laravel’s default). It enables pretty URLs.

### Option B: Only `public_html` available

If you **cannot** change the document root and the site **must** run from `public_html`:

1. Upload the full project to a folder **outside** `public_html`, e.g. `namsaflora`.
2. Copy **everything inside** `public/` (not the `public` folder itself) into `public_html/`.
3. Edit `public_html/index.php` and change the paths:

   **From:**
   ```php
   require __DIR__.'/../vendor/autoload.php';
   $app = require_once __DIR__.'/../bootstrap/app.php';
   ```
   **To:**
   ```php
   require __DIR__.'/../namsaflora/vendor/autoload.php';
   $app = require_once __DIR__.'/../namsaflora/bootstrap/app.php';
   ```
   (Adjust `namsaflora` to your actual folder path relative to `public_html`.)

4. Ensure `public_html/.htaccess` exists (it’s copied from `public/` in step 2).

---

## 4. Set Permissions

Via **SSH** or **File Manager** (or FTP):

- **Folders**: `755`.
- **Files**: `644`.
- **`storage`** and **`bootstrap/cache`**: must be **writable** by the web server. Often:
  ```bash
  chmod -R 775 storage bootstrap/cache
  ```
  If your host uses a specific user (e.g. `nobody`, `apache`), you may need to set ownership. Check your host’s docs.

---

## 5. Run Artisan Commands on the Server

Use **SSH** (if available) or **Terminal** in cPanel:

```bash
cd /home/youruser/namsaflora   # your project path

php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

- **migrate**: creates DB tables.
- **db:seed**: creates admin user and sample data.
- **storage:link**: links `public/storage` → `storage/app/public` (product images, logo).
- **config/route/view cache**: faster production.

**Admin login** (from seeder): `admin@namsa.com.na`, password shown once in the terminal when the seeder runs. Set up
two-factor authentication immediately after first login (mandatory).

---

## 6. HTTPS and `APP_URL`

- In cPanel, install an **SSL certificate** for **namsa.com.na** (e.g. Let’s Encrypt).
- Keep `APP_URL=https://namsa.com.na` in `.env`.
- If the host redirects HTTP → HTTPS automatically, you’re set. Otherwise, add a redirect in `public/.htaccess` (see **Optional** below).

---

## 7. Quick Checklist

| Item | Done |
|------|------|
| PHP 8.2+ and required extensions | ☐ |
| MySQL database and user created | ☐ |
| `.env` production values (DB, mail, `APP_URL`, etc.) | ☐ |
| `composer install --no-dev` run locally | ☐ |
| Project uploaded (including `vendor`, `.env`) | ☐ |
| Document root = `public` (or Option B applied) | ☐ |
| `storage` & `bootstrap/cache` writable | ☐ |
| `migrate`, `db:seed`, `storage:link`, caches run | ☐ |
| SSL on namsa.com.na, `APP_URL` uses `https` | ☐ |
| Admin password changed | ☐ |

---

## 8. Optional

### Force HTTPS in `public/.htaccess`

Add **right after** `RewriteEngine On`:

```apache
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### Cron (required — hourly abandoned-card-payment reminder)

The app schedules an hourly job (`orders:remind-pending`, see `routes/console.php`) that emails customers whose DPO card payment never completed. Laravel's scheduler needs cron to call it every minute; it only actually runs the job on its own schedule.

In cPanel → **Cron Jobs**:

```bash
* * * * * cd /home/youruser/namsaflora && php artisan schedule:run >> /dev/null 2>&1
```

### Troubleshooting

- **500 error**: Check `storage/logs/laravel.log`. Fix permissions on `storage` and `bootstrap/cache`.
- **“No application encryption key”**: Run `php artisan key:generate` and set `APP_KEY` in `.env`.
- **DB connection error**: Verify `DB_*` in `.env` (host often `localhost`).
- **Images/logo missing**: Run `php artisan storage:link` and ensure `storage/app/public` is writable.
- **CSRF / 419 on forms**: Ensure `APP_URL` matches the real domain and HTTPS. Clear config cache: `php artisan config:clear`.

---

## 9. After Go-Live

1. Change admin password: **Admin** → profile/settings.
2. Update logo and products as needed.
3. Test **Contact** form: submit and confirm email arrives at **info@namsa.com.na**.
4. Test **checkout** and **WhatsApp** order flow.

You’re done. The site should be live at **https://namsa.com.na**.
