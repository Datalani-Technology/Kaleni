# Testing Guide - Namsa Flora E-Commerce

## 🚀 Quick Start

### Option 1: Use the START.bat file
Simply double-click `START.bat` in the project folder. This will start the server automatically.

### Option 2: Manual Start
Open terminal/command prompt in the project folder and run:
```bash
php artisan serve
```

Then open your browser to: **http://localhost:8000**

---

## ✅ What's Already Set Up

✓ Database created (`namsa_flora`)  
✓ All tables migrated  
✓ Admin user created  
✓ 8 sample products added  
✓ Storage link created  
✓ Everything configured and ready!

---

## 🧪 Testing Checklist

### 1. Frontend Testing (Customer View)

#### Browse Products
- [ ] Visit http://localhost:8000
- [ ] See the homepage with product listings
- [ ] Click on "Products" in navigation
- [ ] Browse through the 8 sample products
- [ ] Click on a product to see details

#### Shopping Cart
- [ ] Click "View Details" on any product
- [ ] Change quantity (if stock allows)
- [ ] Click "Add to Cart"
- [ ] See success message
- [ ] Click "Cart" in navigation
- [ ] Verify items are in cart
- [ ] Update quantity in cart
- [ ] Remove an item from cart
- [ ] Clear entire cart

#### Checkout Process
- [ ] Add items to cart
- [ ] Click "Proceed to Checkout"
- [ ] Fill in customer information:
  - Name: Test Customer
  - Email: test@example.com
  - Phone: 0812345678
  - Address: 123 Test Street, Windhoek
- [ ] Select payment method (DPO or WhatsApp)
- [ ] Click "Complete Order"
- [ ] See order success page

### 2. Admin Panel Testing

#### Login
- [ ] Visit http://localhost:8000/{your ADMIN_PATH}/login
- [ ] Login with:
  - **Email:** admin@namsa.com.na
  - **Password:** printed once to the terminal when the seeder ran
- [ ] Set up two-factor authentication (required on first login)
- [ ] See admin dashboard

#### Dashboard
- [ ] View statistics:
  - Total Products (should show 8)
  - Total Orders
  - Pending Orders
- [ ] Click "Add New Product" button

#### Product Management
- [ ] Click "Products" in sidebar
- [ ] See list of 8 sample products
- [ ] Click "Add New Product"
- [ ] Fill in product form:
  - Name: Test Flower
  - Description: This is a test product
  - Price: 150.00
  - Stock: 20
  - Category: Test
  - Upload an image (optional)
  - Check "Active"
- [ ] Click "Create Product"
- [ ] See product in list

#### Edit Product
- [ ] Click edit (pencil icon) on any product
- [ ] Change some details
- [ ] Click "Update Product"
- [ ] Verify changes saved

#### Delete Product
- [ ] Click delete (trash icon) on a product
- [ ] Confirm deletion
- [ ] Verify product removed

### 3. Payment Testing

#### DPO Payment (Test Mode)
- [ ] Add items to cart
- [ ] Go to checkout
- [ ] Select "Pay with DPO"
- [ ] Complete order
- [ ] Note: In test mode, actual payment won't process
- [ ] You'll be redirected to DPO test page

#### WhatsApp Payment
- [ ] Add items to cart
- [ ] Go to checkout
- [ ] Select "Pay via WhatsApp"
- [ ] Complete order
- [ ] WhatsApp should open with order details
- [ ] Message includes all order information

### 4. Recommendations Testing

#### View Recommendations
- [ ] Add multiple different products to cart
- [ ] Complete an order
- [ ] Go back to a product you purchased
- [ ] Scroll down to "You May Also Like" section
- [ ] See recommended products based on your purchase

---

## 📊 Sample Data

### Products Already Created:
1. Red Rose Bouquet - N$ 250.00
2. Mixed Flower Arrangement - N$ 350.00
3. White Lily Bouquet - N$ 280.00
4. Sunflower Bouquet - N$ 200.00
5. Orchid Plant - N$ 450.00
6. Pink Rose Bouquet - N$ 270.00
7. Tulip Bouquet - N$ 220.00
8. Carnation Arrangement - N$ 180.00

### Admin Account:
- **Email:** admin@namsa.com.na
- **Password:** printed once to the terminal when the seeder ran (no fixed default)

---

## 🔍 What to Test

### Core Functionality
- ✅ Product browsing and search
- ✅ Shopping cart operations
- ✅ Checkout process
- ✅ Order creation
- ✅ Admin login
- ✅ Product CRUD operations
- ✅ Image uploads (if you add images)
- ✅ Stock management
- ✅ Payment method selection

### Edge Cases
- [ ] Try adding more items than available stock
- [ ] Try checking out with empty cart
- [ ] Try accessing admin without login
- [ ] Try accessing admin with wrong credentials
- [ ] Try deleting a product that's in an order

---

## 🐛 Common Issues & Solutions

### Images Not Showing
- Images are optional - products work without images
- If you upload images, they'll be stored in `storage/app/public/products`
- Make sure storage link exists: `php artisan storage:link`

### Payment Gateway Not Working
- DPO is in test mode - actual payments won't process
- You need real DPO credentials for live payments
- WhatsApp payment opens WhatsApp with order details

### Can't Login to Admin
- Make sure you ran: `php artisan db:seed`
- Check database has admin user
- Admin email: admin@namsa.com.na — password was printed once to the terminal when the
  seeder ran; if you lost it, use "Forgot password" on the login page
- Make sure you're using the correct `ADMIN_PATH` from your `.env`

### Database Errors
- Make sure MySQL is running in XAMPP
- Database should be named: `namsa_flora`
- Check `.env` file has correct database credentials

---

## 📝 Notes

- All sample products are active and in stock
- Cart uses session storage (clears when browser closes)
- Orders are saved to database
- Recommendations improve as more orders are placed
- Admin can manage all products from dashboard

---

## 🎉 You're Ready!

Everything is set up and ready for testing. Just start the server and begin exploring!

**Happy Testing! 🌸**
