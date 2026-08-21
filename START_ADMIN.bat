@echo off
echo ========================================
echo   Namsa Flora - Admin Panel
echo ========================================
echo.
echo Starting admin panel on port 8004...
echo.
echo Admin URL: http://localhost:8004/admin/login
echo.
echo Admin Credentials:
echo   Email: admin@namsa.com.na
echo   Password: admin123
echo.
echo Press Ctrl+C to stop the server
echo.
php artisan serve --port=8004
