@echo off
echo ========================================
echo   Namsa Flora - Starting Both Servers
echo ========================================
echo.
echo Starting main site on port 8003...
echo Starting admin panel on port 8004...
echo.
echo Frontend: http://localhost:8003
echo Admin: http://localhost:8004/{your ADMIN_PATH from .env}/login
echo.
echo Admin Credentials:
echo   Email: admin@namsa.com.na
echo   Password: shown once in the terminal when the seeder ran (no fixed default)
echo.
echo Both servers will open in separate windows...
echo Press any key to continue...
pause >nul
echo.
start "Namsa Flora - Main Site (Port 8003)" cmd /k "cd /d %~dp0 && php artisan serve --port=8003"
timeout /t 2 /nobreak >nul
start "Namsa Flora - Admin Panel (Port 8004)" cmd /k "cd /d %~dp0 && php artisan serve --port=8004"
echo.
echo Both servers are now running!
echo Close the windows or press Ctrl+C in each to stop them.
