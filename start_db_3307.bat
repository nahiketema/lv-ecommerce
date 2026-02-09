@echo off
echo Stopping any conflicting MySQL instances...
taskkill /f /im mysqld.exe 2>nul

echo.
echo Starting Custom MySQL for Louis Vuitton Project...
echo Port: 3307
echo.
echo [IMPORTANT] Do NOT close this window! Minimize it to keep the database running.
echo.

"c:\xampp\mysql\bin\mysqld.exe" --defaults-file="c:\xampp\htdocs\lv-ecommerce\local_mysql.ini" --console --standalone

pause
