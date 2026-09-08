# One-click startup script for PowerShell
Write-Host "Starting Dynamic QR Social Profile SaaS..." -ForegroundColor Cyan
Start-Process php -ArgumentList "artisan serve --port=8000"
Start-Process npm -ArgumentList "run dev"
Write-Host "==================================================" -ForegroundColor Green
Write-Host "  Application running at: http://127.0.0.1:8000" -ForegroundColor Green
Write-Host "==================================================" -ForegroundColor Green
