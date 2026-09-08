@echo off
echo Starting Dynamic QR Social Profile SaaS...
start php artisan serve --port=8000
start npm run dev
echo.
echo ==================================================
echo   Application running at: http://127.0.0.1:8000
echo ==================================================
