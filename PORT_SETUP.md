# Port Configuration Guide

## 🚀 Server Setup

The Kaleni Catering Services e-commerce platform is configured to run on separate ports:

- **Main Site (Frontend)**: Port **8003**
- **Admin Panel**: Port **8004**

---

## 📋 Quick Start Options

### Option 1: Start Both Servers (Recommended)
Double-click **`START_BOTH.bat`**

This will start both servers in separate windows:
- Main site: http://localhost:8003
- Admin panel: http://localhost:8004/{your ADMIN_PATH}/login

### Option 2: Start Main Site Only
Double-click **`START.bat`**

This starts only the customer-facing site on port 8003.

### Option 3: Start Admin Only
Double-click **`START_ADMIN.bat`**

This starts only the admin panel on port 8004.

---

## 🔗 Access URLs

### Customer Site
- **URL**: http://localhost:8003
- **Features**: Browse products, shopping cart, checkout

### Admin Panel
- **URL**: http://localhost:8004/{your ADMIN_PATH}/login
- **Login**: 
  - Email: `kalenilucas061@gmail.com`
  - Password: printed once to the terminal by `AdminUserSeeder` (then set up 2FA — required)

---

## ⚙️ Manual Start (Command Line)

### Start Main Site
```bash
php artisan serve --port=8003
```

### Start Admin Panel
```bash
php artisan serve --port=8004
```

### Start Both (Separate Terminals)
**Terminal 1:**
```bash
php artisan serve --port=8003
```

**Terminal 2:**
```bash
php artisan serve --port=8004
```

---

## 🔧 Configuration

### Change Ports

To change the ports, edit the `.bat` files:

**START.bat:**
```batch
php artisan serve --port=8003
```
Change `8003` to your desired port.

**START_ADMIN.bat:**
```batch
php artisan serve --port=8004
```
Change `8004` to your desired port.

### Update .env
Make sure your `.env` file has:
```
APP_URL=http://localhost:8003
```

---

## 📝 Important Notes

1. **Both servers share the same database** - All data is synchronized
2. **Sessions work independently** - You can be logged into admin and customer site separately
3. **Stop servers** - Press `Ctrl+C` in each window to stop
4. **Port conflicts** - If ports are in use, change them in the `.bat` files

---

## 🐛 Troubleshooting

### Port Already in Use
If you get "Address already in use" error:
1. Close any existing Laravel servers
2. Change the port number in the `.bat` file
3. Update `APP_URL` in `.env` if needed

### Can't Access Admin
- Make sure `START_ADMIN.bat` is running
- Check the URL: http://localhost:8004/{your ADMIN_PATH}/login
- Verify admin user exists: Run `php artisan db:seed --class=AdminUserSeeder`

### Sessions Not Working
- Both servers use the same database for sessions
- Clear browser cache if issues persist
- Check `SESSION_DRIVER=database` in `.env`

---

## ✅ Verification

After starting both servers, verify:

1. **Main Site**: Open http://localhost:8003
   - Should show product listing
   - Navigation should work

2. **Admin Panel**: Open http://localhost:8004/{your ADMIN_PATH}/login
   - Should show login page
   - Login with admin credentials
   - Should access dashboard

---

## 🎯 Benefits of Separate Ports

- **Security**: Admin panel isolated from public site
- **Performance**: Can run independently
- **Development**: Easier to test separately
- **Deployment**: Can deploy to different servers in production

---

Happy coding! 🌸
