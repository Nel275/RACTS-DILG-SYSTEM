@echo off
REM Copies barangay folders and report files from the live RACTS site to this computer.
cd /d "%~dp0"
php artisan racts:sync-from-live
echo.
pause
