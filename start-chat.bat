@echo off
cd /d D:\baitapcuoiky\fullstack
echo.
echo ================== CHAT REALTIME SERVER ==================
echo Starting WebSocket server on ws://localhost:8080
echo Starting PHP server on http://localhost:3000
start php -S localhost:3000
timeout /t 2
php websocket\server.php
pause