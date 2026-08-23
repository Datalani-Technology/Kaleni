# Local Test → Then Deploy to Production

Use this to **test everything locally first**, then **switch to production details** and host again.

---

## Part 1: Test Locally

### 1. Prerequisites

- **XAMPP**: Apache + MySQL running (or MySQL only if you use `php artisan serve`).
- **PHP 8.2+** (XAMPP’s PHP).
- **Composer** installed.

### 2. Database (local)

1. Open **phpMyAdmin**: http://localhost/phpmyadmin  
2. Create a database: **`namsa_flora`**  
3. Leave user **`root`**, password **blank** (default XAMPP).

### 3. Environment (.env) for local

1. Copy `.env.example` → `.env` if you don’t have `.env` yet.  
2. Generate key if missing:
   ```bash
   php artisan key:generate
   ```
3. Set these in **`.env`** for **local**:

   ```env
   APP_ENV=local
   APP_DEBUG=true
   APP_URL=http://localhost:8003

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=namsa_flora
   DB_USERNAME=root
   DB_PASSWORD=

   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=database

   # Local: log mail instead of sending (no SMTP)
   MAIL_MAILER=log
   MAIL_FROM_ADDRESS="info@namsa.com.na"
   MAIL_FROM_NAME="/Namsa Florals"

   CONTACT_PHONE=+264815574680
   CONTACT_EMAIL_INFO=info@namsa.com.na
   CONTACT_EMAIL_ORDERS=order@namsa.com.na

   WHATSAPP_PAYMENT_NUMBER=264815574680
   SOCIAL_WHATSAPP=https://wa.me/264815574680
   ```

   Leave **CONTACT_*** and **SOCIAL_*** as above for local; they’re only used for display and contact-form recipient.

### 4. Install deps, migrate, seed, storage

Run in the project folder (`c:\xampp\htdocs\namsa flora`):

```bash
composer install
php artisan migrate
php artisan db:seed
php artisan storage:link
```

### 5. Start the app

**Option A – Batch file (recommended)**  
- Double‑click **`START.bat`**  
- Site runs at **http://localhost:8003**

**Option B – Command line**

```bash
php artisan serve --port=8003
```

Then open **http://localhost:8003** in your browser.

### 6. Admin login (local)

| Field    | Value                    |
|----------|--------------------------|
| **URL**  | http://localhost:8003/`{ADMIN_PATH from .env}`/login |
| **Email**| `admin@namsa.com.na`     |
| **Password** | shown once in the terminal when `AdminUserSeeder` runs |

*(Set up two-factor authentication on first login — required before you can use the rest of the panel.)*

### 7. What to test locally

**Frontend**

- [ ] **Home**: http://localhost:8003  
- [ ] **Products**: http://localhost:8003/products  
- [ ] **Gallery**: http://localhost:8003/gallery  
- [ ] **Promotion**: http://localhost:8003/promotion  
- [ ] **Contact**: http://localhost:8003/contact  
- [ ] **Cart**: add product → cart → checkout flow  
- [ ] **Contact form**: submit a message  
  - With `MAIL_MAILER=log`, no email is sent; the form should succeed and you’ll see “Thank you for your message!”  
  - Check **`storage/logs/laravel.log`** for the logged “email”.

**Admin** (after login at your `ADMIN_PATH` URL)

- [ ] **Dashboard**: stats, quick actions  
- [ ] **Orders**: list, open one, update status  
- [ ] **Products**: list, add, edit, delete, **bulk delete**  
- [ ] **Stock**: view and adjust  
- [ ] **Enquiries**: contact submissions  
- [ ] **Gallery**: list, **Add Item**, edit, delete  
- [ ] **Promotion Catalog**: upload/replace/remove PDF  
- [ ] **Logo**: upload (admin-only if restricted)  
- [ ] **Users**: manage (admin-only)

### 8. Optional: two ports (main + “admin”)

- **`START_BOTH.bat`**: main on **8003**, second server on **8004**.  
- Use **http://localhost:8004** for “admin” if you like; same app, same admin login, at your
  configured `ADMIN_PATH`.

---

## Part 2: Change to production details, then host

### 1. What to change for production

**`.env` (or the one used in `public_html_ready`)**:

| Setting | Local (example) | Production |
|--------|------------------|------------|
| `APP_ENV` | `local` | `production` |
| `APP_DEBUG` | `true` | `false` |
| `APP_URL` | `http://localhost:8003` | `https://namsa.com.na` |
| `DB_HOST` | `127.0.0.1` | `localhost` (or host’s MySQL host) |
| `DB_DATABASE` | `namsa_flora` | Your cPanel DB name (e.g. `namsacomna258_namsa`) |
| `DB_USERNAME` | `root` | Your cPanel MySQL user |
| `DB_PASSWORD` | *(blank)* | Your cPanel MySQL password |
| `MAIL_MAILER` | `log` | `smtp` |
| `MAIL_HOST` | - | `mail.namsa.com.na` (or your host’s SMTP) |
| `MAIL_PORT` | - | `587` (or `465` for SSL) |
| `MAIL_USERNAME` | - | e.g. `info@namsa.com.na` |
| `MAIL_PASSWORD` | - | SMTP password |
| `MAIL_ENCRYPTION` | - | `tls` or `ssl` |
| `MAIL_FROM_ADDRESS` | `info@namsa.com.na` | Same (or your from address) |
| `CONTACT_EMAIL_INFO` | `info@namsa.com.na` | Inbox for contact form |
| `CONTACT_PHONE` | `+264815574680` | Keep or update |
| `WHATSAPP_*` / `SOCIAL_*` | As above | Keep or update |

**`deploy-db.env` (optional)**  
Create a file **`deploy-db.env`** in the project root (it’s gitignored) with your **production** DB and mail overrides. **`build-deploy.php`** will use these when building **`public_html_ready/.env`**:

```env
DB_HOST=localhost
DB_DATABASE=your_cpanel_db_name
DB_USERNAME=your_cpanel_db_user
DB_PASSWORD=your_cpanel_db_password
MAIL_PASSWORD=your_smtp_password
```

Then run **`build-deploy.php`**. See **`DEPLOY_SHARED_HOSTING.md`** for the full deploy flow.

### 2. Build deploy package

```bash
php build-deploy.php
```

- Uses your **.env** and **deploy-db.env** (if present).  
- Creates **`public_html_ready/`** (app, vendor, **database/dump.sql**, uploads, etc.).

### 3. Edit **`public_html_ready/.env`** for the host

- Set **DB_*** and **MAIL_*** as in the table above for your hosting.  
- Set **APP_URL=https://namsa.com.na**.

### 4. On the host (cPanel)

1. **MySQL**: Create database + user, assign user to database.  
2. **phpMyAdmin** → your DB → **Import** → **`database/dump.sql`** from the package.  
3. **Upload** everything **inside** `public_html_ready/` into **public_html** (so `index.php` and `.htaccess` are at the root).  
4. **Permissions**: **storage** (recursive), **bootstrap/cache**, **uploads** (and subdirs) → **775**.

### 5. After deploy

- Visit **https://namsa.com.na** and **https://namsa.com.na/`{your ADMIN_PATH}`/login**.  
- Log in with **admin@namsa.com.na** and the password printed once by the seeder, then set up
  **two-factor authentication** (required before the panel is usable).  
- Test **Gallery**, **Promotion**, **Contact form**, and **Products** again on the live site.

---

## Quick reference

| Item | Local | Production |
|------|--------|------------|
| **Site** | http://localhost:8003 | https://namsa.com.na |
| **Admin** | http://localhost:8003/`{ADMIN_PATH}`/login | https://namsa.com.na/`{ADMIN_PATH}`/login |
| **Admin email** | admin@namsa.com.na | *(same; rotate password after deploy)* |
| **Admin password** | printed once by `AdminUserSeeder` | *(same, or reset via "Forgot password")* |
| **Mail** | `MAIL_MAILER=log` (no send) | `MAIL_MAILER=smtp` + host SMTP |
| **DB** | Local MySQL `namsa_flora` | cPanel MySQL |

For more detail on hosting, see **`DEPLOY_SHARED_HOSTING.md`** and **`README_DEPLOY.txt`** inside **`public_html_ready/`** after you run **`build-deploy.php`**.
