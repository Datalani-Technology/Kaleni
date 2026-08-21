# Namsa Flora - Flower E-Commerce Platform

A beautiful and modern flower selling e-commerce website built with Laravel, featuring admin product management, shopping cart, checkout with DPO Namibia payment gateway and WhatsApp payment options, and intelligent product recommendations.

## Features

- 🌸 **Product Catalog**: Browse beautiful flowers with detailed descriptions and images
- 🛒 **Shopping Cart**: Add products to cart and manage quantities
- 💳 **Payment Options**:
  - DPO Namibia payment gateway integration
  - WhatsApp payment option
- 👨‍💼 **Admin Panel**: 
  - Secure admin login
  - Product management (Create, Read, Update, Delete)
  - Order management dashboard
- 🤖 **Smart Recommendations**: Product recommendations based on user purchase behavior
- 📱 **Responsive Design**: Beautiful, modern UI that works on all devices

## Requirements

- PHP >= 8.2
- MySQL >= 5.7
- Composer
- XAMPP (or similar local server environment)

## Installation

1. **Clone or navigate to the project directory**
   ```bash
   cd "C:\xampp\htdocs\namsa flora"
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   - Create a MySQL database named `namsa_flora` in phpMyAdmin
   - Update `.env` file with your database credentials:
     ```
     DB_DATABASE=namsa_flora
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
     CONTACT_PHONE=+264815574680
     CONTACT_EMAIL_INFO=info@namsa.com.na
     CONTACT_EMAIL_ORDERS=order@namsa.com.na
     WHATSAPP_PAYMENT_NUMBER=264815574680
     SOCIAL_WHATSAPP=https://wa.me/264815574680
     ```
   - Production domain: **namsa.com.na**. Set `APP_URL=https://namsa.com.na`.

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
   - Admin Login: http://localhost:8000/admin/login
     - Email: admin@namsa.com.na
     - Password: admin123

## Database Structure

- **users**: User accounts (admin and customers)
- **products**: Flower products with images, prices, and stock
- **cart_items**: Shopping cart items (session-based)
- **orders**: Customer orders
- **order_items**: Individual items in each order
- **product_recommendations**: Product recommendation scores

## Admin Features

### Product Management
- Create new products with images
- Edit existing products
- Delete products
- Manage stock quantities
- Activate/deactivate products

### Dashboard
- View total products count
- View total orders
- View pending orders
- Quick access to product management

## Payment Integration

### DPO Payment Gateway
The system integrates with DPO (Direct Pay Online) payment gateway for Namibia. Configure your DPO credentials in the `.env` file. The integration supports:
- Credit/Debit card payments
- Payment verification
- Order status updates

### WhatsApp Payment
Customers can choose to pay via WhatsApp. The system generates a formatted message with order details and opens WhatsApp with the configured number.

## Product Recommendations

The system automatically tracks product associations based on:
- Products purchased together
- User browsing behavior
- Purchase history

Recommendations appear on product detail pages to help customers discover related items.

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── AuthController.php
│   │   │   └── ProductController.php
│   │   ├── CartController.php
│   │   ├── CheckoutController.php
│   │   ├── PaymentController.php
│   │   └── ProductController.php
│   └── Middleware/
│       └── AdminMiddleware.php
├── Models/
│   ├── CartItem.php
│   ├── Order.php
│   ├── OrderItem.php
│   ├── Product.php
│   ├── ProductRecommendation.php
│   └── User.php
database/
├── migrations/
└── seeders/
    └── AdminUserSeeder.php
resources/
└── views/
    ├── admin/
    ├── cart/
    ├── checkout/
    ├── layouts/
    └── products/
```

## Security Notes

- Change the default admin password after first login
- Keep DPO credentials secure
- Use environment variables for sensitive data
- Regularly update dependencies

## Support

For issues or questions, please contact the development team.

## License

This project is proprietary software for Namsa Flora.
