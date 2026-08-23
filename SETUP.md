# Setup Guide for Namsa Flora E-Commerce

## Quick Start Guide

### Step 1: Database Setup
1. Open phpMyAdmin (usually at http://localhost/phpmyadmin)
2. Create a new database named `namsa_flora`
3. The database will be automatically created when you run migrations

### Step 2: Environment Configuration
1. The `.env` file is already configured with:
   - Database name: `namsa_flora`
   - Database user: `root`
   - Database password: (empty by default in XAMPP)

2. Update DPO Payment Gateway credentials (if you have them):
   ```
   DPO_COMPANY_TOKEN=your-actual-token
   DPO_SERVICE_TYPE=your-service-type
   DPO_TEST_MODE=true
   ```

3. Contact & WhatsApp (used site-wide):
   ```
   CONTACT_PHONE=+264815574680
   CONTACT_EMAIL_INFO=info@namsa.com.na
   CONTACT_EMAIL_ORDERS=order@namsa.com.na
   WHATSAPP_PAYMENT_NUMBER=264815574680
   SOCIAL_WHATSAPP=https://wa.me/264815574680
   ```
   Domain: **namsa.com.na**. Set `APP_URL=https://namsa.com.na` for production.

4. Admin panel URL: set a private, unguessable `ADMIN_PATH` (never the default `admin`) —
   this becomes the only entry point to the admin panel and isn't listed anywhere public.
   ```
   ADMIN_PATH=your-own-secret-slug
   ```

### Step 3: Run Migrations and Seeders
Open terminal/command prompt in the project directory and run:

```bash
php artisan migrate
php artisan db:seed
```

This will:
- Create all database tables
- Create an admin user with:
  - Email: `admin@namsa.com.na`
  - Password: randomly generated and printed once to the terminal — copy it now

### Step 4: Create Storage Link
Run this command to enable image uploads:

```bash
php artisan storage:link
```

### Step 5: Start the Server
```bash
php artisan serve
```

The application will be available at: http://localhost:8000

## Access Points

- **Frontend (Customer)**: http://localhost:8000
- **Admin Login**: http://localhost:8000/`{your ADMIN_PATH}`/login
  - Email: `admin@namsa.com.na`
  - Password: printed once by the seeder (see Step 3)

## First Steps After Setup

1. **Login as Admin**
   - Go to http://localhost:8000/`{your ADMIN_PATH}`/login
   - Use the credentials above, then set up two-factor authentication (required)

2. **Add Products**
   - Click on "Products" in the sidebar
   - Click "Add New Product"
   - Fill in product details and upload an image
   - Save the product

3. **Test the Frontend**
   - Visit http://localhost:8000
   - Browse products
   - Add items to cart
   - Test checkout process

## Important Notes

### DPO Payment Gateway
- The DPO integration is set up but requires actual credentials from DPO
- In test mode, you can test the flow but actual payments won't process
- Contact DPO Namibia to get your credentials

### WhatsApp & Contact
- Default phone: `+264815574680`. Emails: `info@namsa.com.na`, `order@namsa.com.na`
- Override via `CONTACT_PHONE`, `CONTACT_EMAIL_INFO`, `CONTACT_EMAIL_ORDERS` in `.env`
- `WHATSAPP_PAYMENT_NUMBER` and `SOCIAL_WHATSAPP` use the same number (264815574680)

### Contact form → info@
- "Send us a Message" submissions are stored in DB and emailed to `CONTACT_EMAIL_INFO` (info@namsa.com.na).
- **To receive real emails:** set `MAIL_MAILER=smtp` and configure `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD` (e.g. Gmail SMTP or your provider). Use `MAIL_FROM_ADDRESS` / `MAIL_FROM_NAME` as the sender.
- **Otherwise:** keep `MAIL_MAILER=log`; emails are written to `storage/logs/laravel.log` only.

### Image Uploads
- Product images are stored in `storage/app/public/products`
- Make sure the storage link is created (Step 4)
- Supported formats: JPEG, PNG, JPG, GIF
- Max file size: 2MB

### Stock Count & Inventory
- Admin **Stock Count** page tracks inventory per product. Use **Set to** (after physical count), **Add**, or **Subtract**, with optional reason. Changes are logged in `stock_movements`.
- Low-stock threshold: products with stock ≤ `INVENTORY_LOW_STOCK_THRESHOLD` (default 5) are highlighted. Set in `.env` if needed.

### Security
- **IMPORTANT**: Change the admin password immediately after first login
- Keep your `.env` file secure and never commit it to version control
- Use strong passwords for production
- **In place:** CSRF on all forms, security headers (CSP, X-Frame-Options, etc.), rate limiting (contact form, admin login, checkout), validation + sanitization on contact/checkout, honeypot + math check on contact form. Admin routes protected by `auth` + `admin` middleware.

## Troubleshooting

### Database Connection Error
- Make sure MySQL is running in XAMPP
- Check database credentials in `.env`
- Verify database `namsa_flora` exists

### Images Not Showing
- Run `php artisan storage:link` again
- Check file permissions on `storage` and `public` directories
- Ensure images are uploaded to `storage/app/public/products`

### Admin Login Not Working
- Make sure you ran `php artisan db:seed`
- Check if user exists in database: `SELECT * FROM users WHERE email = 'admin@namsa.com.na'`
- Try resetting password manually in database

### Payment (WhatsApp vs DPO)
- **Current:** Checkout is **WhatsApp-only**. Customers complete the form, then are sent to WhatsApp to confirm and arrange payment. No card payment until DPO provides keys.
- **When DPO is ready:** Re-enable the "Pay with DPO" option in `resources/views/checkout/index.blade.php`, add `dpo` back to `payment_method` validation in `CheckoutController`, and set `DPO_COMPANY_TOKEN` etc. in `.env`.

### Payment Gateway Issues (DPO)
- Verify DPO credentials are correct
- Check if test mode is enabled for testing
- Review payment controller logs

## Next Steps

1. Customize the design to match your brand
2. Add more products
3. Configure email notifications (optional)
4. Set up production environment
5. Get DPO payment gateway credentials for live payments

## Support

For technical support or questions, refer to the main README.md file.
