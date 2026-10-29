@echo off
title MAGKOM - Laravel Server (Laragon / PHP 8.3)
echo ===================================================
echo     Menjalankan Portal MAGKOM dengan Laragon
echo ===================================================
echo.
echo Pastikan service MySQL di Laragon sudah AKTIF (Start All)!
echo.

set PHP_LARAGON=D:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe
set PHP_LARAGON_C=C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe
set PHP_XAMPP=C:\xampp\php\php.exe

if exist "%PHP_LARAGON%" (
    set PHP_BIN=%PHP_LARAGON%
    echo [INFO] Menggunakan PHP Laragon: %PHP_LARAGON%
) else if exist "%PHP_LARAGON_C%" (
    set PHP_BIN=%PHP_LARAGON_C%
    echo [INFO] Menggunakan PHP Laragon: %PHP_LARAGON_C%
) else if exist "%PHP_XAMPP%" (
    set PHP_BIN=%PHP_XAMPP%
    echo [INFO] Menggunakan PHP XAMPP: %PHP_XAMPP%
) else (
    set PHP_BIN=php
    echo [INFO] Menggunakan PHP default sistem
)

echo Server aktif di: http://127.0.0.1:8000
echo Buka di browser: http://127.0.0.1:8000/peta-alur
echo Tekan Ctrl+C untuk menghentikan server.
echo ---------------------------------------------------
"%PHP_BIN%" artisan serve --host=127.0.0.1 --port=8000

pause
