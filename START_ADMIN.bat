@echo off
echo ========================================
echo   Kaleni Catering Services - Admin Panel
echo ========================================
echo.
echo Starting admin panel on port 8004...
echo.
echo Admin URL: http://localhost:8004/{your ADMIN_PATH from .env}/login
echo.
echo Admin Credentials:
echo   Email: kalenilucas061@gmail.com
echo   Password: shown once in the terminal when the seeder ran (no fixed default)
echo.
echo Press Ctrl+C to stop the server
echo.
php artisan serve --port=8004
