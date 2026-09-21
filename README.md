# Kaleni Catering Services — Booking & Ordering Platform

A modern catering booking and ordering platform for **Kaleni Catering Services** (Chef K), built with Laravel. Clients browse the menu, see a daily **Food of the Day**, order lunch/dinner packs, book full event catering, or send a bespoke **Special Request**. Includes an admin console for menu, bookings, stock, promo codes, expenses, reports and analytics.

## Features

- 🍽️ **Menu Catalog**: Browse home-style dishes, packs, and platters with descriptions, prices, and photos
- ⭐ **Food of the Day**: A different daily special, scheduled by admins, shown on the homepage and its own page
- 📝 **Event Bookings**: Clients pick an event date, serving period (breakfast/lunch/dinner/full day/custom), guest count, and event type
- 💬 **Special Requests**: A form for bespoke dishes/menus not on the standing menu, quoted by staff
- 🛒 **Order Builder**: Add menu items to an in-progress order before booking
- 💳 **Payment Options**:
  - DPO Namibia payment gateway integration
  - WhatsApp payment/confirmation option
- 👨‍💼 **Admin Panel**:
  - Secure admin login with two-factor authentication
  - Menu item management (Create, Read, Update, Delete)
  - Booking management dashboard
  - Food of the Day scheduling
  - Special request tracking (status, quotes, client notifications)
- 🤖 **Smart Recommendations**: "Often ordered together" menu suggestions based on past bookings
- 📱 **Responsive Design**: Modern UI that works on all devices

## Requirements

- PHP >= 8.2
- MySQL >= 5.7
- Composer
- XAMPP (or similar local server environment)

## Installation

1. **Clone or navigate to the project directory**
   ```bash
   cd "C:\xampp\htdocs\kaleni-catering"
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   - Create a MySQL database named `kaleni_database` in phpMyAdmin
   - Update `.env` file with your database credentials:
     ```
     DB_DATABASE=kaleni_database
     DB_USERNAME=root
     DB_PASSWORD=your_password
     ```

4. **Configure Payment Gateways**
   - Update `.env` file with your DPO credentials:
     ```
     DPO_COMPANY_TOKEN=your-company-token
     DPO_SERVICE_TYPE=your-service-type
     DPO_TEST_MODE=true
     ```
   - Contact & WhatsApp (see `.env.example`):
     ```
     CONTACT_PHONE=+264813382817
     CONTACT_EMAIL_INFO=kalenilucas061@gmail.com
     CONTACT_EMAIL_ORDERS=kalenilucas061@gmail.com
     WHATSAPP_PAYMENT_NUMBER=264813382817
     SOCIAL_WHATSAPP=https://wa.me/264813382817
     ```
   - Set `APP_URL` to your real production domain once one exists.

5. **Run migrations and seeders**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

6. **Create storage link for images**
   ```bash
   php artisan storage:link
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Access the application**
   - Frontend: http://localhost:8000
   - Admin Login: http://localhost:8000/`{your ADMIN_PATH}`/login (set `ADMIN_PATH` in `.env`)
     - Email: kalenilucas061@gmail.com *(seeder default — change this on first login)*
     - Password: printed once to the terminal when `AdminUserSeeder` runs; set up
       two-factor authentication on first login (required)

## Database Structure

- **users**: User accounts (admin and staff)
- **menu_items**: Catering menu items with images, prices, and stock
- **cart_items**: In-progress order items (session-based)
- **bookings**: Client bookings for an event/period
- **booking_items**: Individual items in each booking
- **menu_item_recommendations**: "Often ordered together" scores
- **food_of_the_day**: The scheduled daily special per date
- **special_requests**: Bespoke item/event requests from clients

## Admin Features

### Menu Management
- Create new menu items with images, pricing unit, and serving size
- Edit existing menu items
- Delete menu items
- Manage stock quantities
- Activate/deactivate or feature menu items

### Food of the Day
- Schedule a daily special per calendar date
- Link to an existing menu item, or post a one-off dish with its own title/price/image

### Special Requests
- Review bespoke requests from clients
- Track status (new → in review → quoted → accepted/declined)
- Email the client automatically when a quote is ready

### Dashboard
- View total menu items, bookings, and pending bookings
- Revenue, expenses, and net profit at a glance
- Quick access to menu and booking management

## Payment Integration

### DPO Payment Gateway
The system integrates with DPO (Direct Pay Online) payment gateway for Namibia. Configure your DPO credentials in the `.env` file. The integration supports:
- Credit/Debit card payments
- Payment verification
- Booking status updates

### WhatsApp Payment
Clients can choose to confirm via WhatsApp. The system generates a formatted message with booking details and opens WhatsApp with the configured number.

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AuthController.php
│   │   │   ├── MenuItemController.php
│   │   │   ├── BookingController.php
│   │   │   ├── FoodOfTheDayController.php
│   │   │   └── SpecialRequestController.php
│   │   ├── CartController.php
│   │   ├── BookingController.php
│   │   ├── FoodOfTheDayController.php
│   │   ├── SpecialRequestController.php
│   │   ├── PaymentController.php
│   │   └── MenuController.php
│   ├── Requests/
│   │   ├── StoreBookingRequest.php
│   │   └── StoreSpecialRequestRequest.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── CartItem.php
│   ├── Booking.php
│   ├── BookingItem.php
│   ├── MenuItem.php
│   ├── MenuItemRecommendation.php
│   ├── FoodOfTheDay.php
│   ├── SpecialRequest.php
│   └── User.php
database/
├── migrations/
└── seeders/
    └── AdminUserSeeder.php
resources/
└── views/
    ├── admin/
    ├── cart/
    ├── booking/
    ├── food-of-the-day/
    ├── special-requests/
    ├── layouts/
    └── menu/
```

## Testing

```bash
php artisan test
```

Feature tests cover the booking flow (stock checks, promo codes), Food of the Day resolution, special request submission, and admin authentication/2FA.

## Security Notes

- Change the default admin password after first login
- Keep DPO credentials secure
- Use environment variables for sensitive data
- Regularly update dependencies

## Support

For issues or questions, contact Kaleni Catering Services at kalenilucas061@gmail.com or the development team.

## License

This project is proprietary software for Kaleni Catering Services.
