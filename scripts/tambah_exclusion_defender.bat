@echo off
title Optimasi MagKom - Windows Defender Exclusion
echo ====================================================================
echo Menambahkan Folder Laragon dan MAGKOM ke Pengecualian Defender...
echo ====================================================================
powershell -Command "Start-Process powershell -Verb RunAs -ArgumentList '-NoProfile -Command Add-MpPreference -ExclusionPath ''F:\laragon'', ''G:\MAGKOM''; Write-Host ''[SUKSES] Folder F:\laragon dan G:\MAGKOM berhasil ditambahkan ke Exclusion Defender!'' -ForegroundColor Green; Start-Sleep -Seconds 3'"
echo Proses selesai.
