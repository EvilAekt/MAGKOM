@echo off
title MAGKOM - Laravel Server (XAMPP PHP)
echo ===================================================
echo     Menjalankan Portal MAGKOM dengan XAMPP
echo ===================================================
echo.
echo Pastikan service MySQL di XAMPP Control Panel sudah AKTIF!
echo.

set PHP_BIN=C:\xampp\php\php.exe

if exist "%PHP_BIN%" (
    echo Menggunakan PHP XAMPP: %PHP_BIN%
    echo Server aktif di: http://127.0.0.1:8000
    echo Tekan Ctrl+C untuk menghentikan server.
    echo ---------------------------------------------------
    "%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000
) else (
    echo [Peringatan] C:\xampp\php\php.exe tidak ditemukan, mencoba menggunakan php default sistem...
    php artisan serve --host=127.0.0.1 --port=8000
)

pause
