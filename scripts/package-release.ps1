<#
.SYNOPSIS
    Skrip Pengemasan Rilis Aman MAGKOM (Secure Release Packager)
.DESCRIPTION
    Mengemas kode sumber aplikasi ke dalam arsip ZIP distribusi rilis production
    dengan pengecualian ketat terhadap berkas kredensial, environment lokal,
    log, cadangan database, berkas diagnostik, dan direktori kerja sementara.
.PARAMETER OutputZip
    Lokasi dan nama berkas ZIP hasil pengemasan.
#>

[CmdletBinding()]
param(
    [string]$OutputZip = ""
)

$ErrorActionPreference = "Stop"

Add-Type -AssemblyName System.IO.Compression.FileSystem

$repoRoot = Resolve-Path (Join-Path $PSScriptRoot "..")
Set-Location $repoRoot

if ([string]::IsNullOrWhiteSpace($OutputZip)) {
    $timestamp = Get-Date -Format "yyyyMMdd-HHmmss"
    $distDir = Join-Path $repoRoot "dist"
    if (-not (Test-Path $distDir)) {
        New-Item -ItemType Directory -Path $distDir -Force | Out-Null
    }
    $OutputZip = Join-Path $distDir "magkom-release-$timestamp.zip"
} else {
    $parentDir = Split-Path -Path $OutputZip -Parent
    if (-not [string]::IsNullOrWhiteSpace($parentDir) -and -not (Test-Path $parentDir)) {
        New-Item -ItemType Directory -Path $parentDir -Force | Out-Null
    }
}

Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "  MAGKOM SECURE RELEASE PACKAGER                               " -ForegroundColor Cyan
Write-Host "================================================================" -ForegroundColor Cyan
Write-Host "Direktori Sumber : $repoRoot"
Write-Host "Target Arsip ZIP : $OutputZip"

# Buat direktori sementara untuk staging
$stagingDir = Join-Path ([System.IO.Path]::GetTempPath()) ("magkom-pkg-" + [System.Guid]::NewGuid().ToString("N"))
New-Item -ItemType Directory -Path $stagingDir -Force | Out-Null

try {
    Write-Host "`n1. Menyiapkan berkas staging..." -ForegroundColor Yellow

    # Pola pengecualian mutlak
    $excludePatterns = @(
        '^\.git(\\|$)',
        '^\.github(\\|$)',
        '^\.agents(\\|$)',
        '^\.env(\..+)?$',         # Menolak .env, .env.backup, .env.production dll
        '^\.mcp\.json$',
        '^\.phpunit',
        '^\.styleci',
        '^\.editorconfig$',
        '^\.gitleaks',
        '^\.semgrep',
        '^scratch(\\|$)',
        '^graphify-out(\\|$)',
        '^\.strix(\\|$)',
        '^strix_runs(\\|$)',
        '^\.testsprite(\\|$)',
        '^testsprite_out(\\|$)',
        '^node_modules(\\|$)',
        '^vendor(\\|$)',
        '^dist(\\|$)',
        '^storage\\logs\\.+\.log$',
        '^storage\\app\\backups(\\|$)',
        '^storage\\app\\private(\\|$)',
        '^storage\\app\\temp(\\|$)',
        '^storage\\app\\public\\profil-peserta(\\|$)',
        '^storage\\app\\public\\foto-staf(\\|$)',
        '^storage\\app\\public\\landing(\\|$)',
        '^storage\\framework\\cache\\data(\\|$)',
        '^storage\\framework\\sessions\\.+',
        '^storage\\framework\\views\\.+',
        '^tests\\check_status\.php$',
        '^tests\\reset_data\.php$'
    )

    $allFiles = Get-ChildItem -Path $repoRoot -Recurse -File

    $copiedCount = 0
    foreach ($file in $allFiles) {
        $relativePath = $file.FullName.Substring($repoRoot.Path.Length + 1)

        # Kecualikan jika cocok dengan pola terlarang
        $isExcluded = $false
        foreach ($pattern in $excludePatterns) {
            if ($relativePath -match $pattern) {
                # Pengecualian khusus: .env.example WAJIB tetap disertakan
                if ($relativePath -eq ".env.example") {
                    continue
                }
                $isExcluded = $true
                break
            }
        }

        if ($isExcluded) {
            continue
        }

        $destPath = Join-Path $stagingDir $relativePath
        $destParent = Split-Path -Path $destPath -Parent
        if (-not (Test-Path $destParent)) {
            New-Item -ItemType Directory -Path $destParent -Force | Out-Null
        }

        Copy-Item -Path $file.FullName -Destination $destPath -Force
        $copiedCount++
    }

    Write-Host "   Total berkas disalin ke staging: $copiedCount berkas."

    # Pastikan struktur folder storage/ dan bootstrap/cache/ tetap ada dengan .gitignore
    Write-Host "`n2. Memastikan struktur skeleton direktori storage..." -ForegroundColor Yellow
    $skeletonDirs = @(
        "storage/app/public",
        "storage/app/private",
        "storage/app/backups",
        "storage/framework/cache/data",
        "storage/framework/sessions",
        "storage/framework/testing",
        "storage/framework/views",
        "storage/logs",
        "bootstrap/cache"
    )

    foreach ($dir in $skeletonDirs) {
        $targetDir = Join-Path $stagingDir $dir
        if (-not (Test-Path $targetDir)) {
            New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
        }
        $gitIgnorePath = Join-Path $targetDir ".gitignore"
        if (-not (Test-Path $gitIgnorePath)) {
            Set-Content -Path $gitIgnorePath -Value "*`n!.gitignore`n"
        }
    }

    # Hapus arsip lama jika ada
    if (Test-Path $OutputZip) {
        Remove-Item -Path $OutputZip -Force
    }

    Write-Host "`n3. Membuat arsip ZIP rilis: $OutputZip" -ForegroundColor Yellow
    [System.IO.Compression.ZipFile]::CreateFromDirectory($stagingDir, $OutputZip)

    Write-Host "`n4. Memverifikasi integritas dan keamanan isi arsip ZIP..." -ForegroundColor Yellow
    $zip = [System.IO.Compression.ZipFile]::OpenRead($OutputZip)

    $hasEnv = $false
    $hasMcp = $false
    $hasEnvExample = $false
    $hasBackups = $false
    $hasPrivate = $false
    $hasLogs = $false

    foreach ($entry in $zip.Entries) {
        $name = $entry.FullName.Replace('/', '\')
        if ($name -eq ".env" -or $name -like "*.env" -or $name -like ".env.*") {
            if ($name -eq ".env.example") {
                $hasEnvExample = $true
            } else {
                $hasEnv = $true
                Write-Host "   [CRITICAL LEAK] Berkas terlarang ditemukan: $name" -ForegroundColor Red
            }
        }
        if ($name -eq ".mcp.json") {
            $hasMcp = $true
            Write-Host "   [CRITICAL LEAK] .mcp.json ditemukan di ZIP!" -ForegroundColor Red
        }
        if ($name -like "storage\app\backups\*" -and -not ($name -like "*\.gitignore")) {
            $hasBackups = $true
        }
        if ($name -like "storage\app\private\*" -and -not ($name -like "*\.gitignore")) {
            $hasPrivate = $true
        }
        if ($name -like "storage\logs\*.log") {
            $hasLogs = $true
        }
    }
    $zip.Dispose()

    if ($hasEnv -or $hasMcp -or $hasBackups -or $hasPrivate -or $hasLogs -or (-not $hasEnvExample)) {
        throw "Verifikasi arsip ZIP rilis GAGAL! Ditemukan kebocoran berkas sensitif atau ketiadaan .env.example."
    }

    Write-Host "   [PASS] .env lokal dikecualikan secara sempurna." -ForegroundColor Green
    Write-Host "   [PASS] .mcp.json dikecualikan." -ForegroundColor Green
    Write-Host "   [PASS] .env.example tersedia dalam arsip." -ForegroundColor Green
    Write-Host "   [PASS] storage/logs, backups, dan private bersih." -ForegroundColor Green
    Write-Host "`nPAKET RILIS AMAN BERHASIL DIBUAT:" -ForegroundColor Green
    Write-Host "Lokasi : $OutputZip" -ForegroundColor Green
    Write-Host "Ukuran : $((Get-Item $OutputZip).Length / 1MB) MB" -ForegroundColor Green
} finally {
    if (Test-Path $stagingDir) {
        Remove-Item -Path $stagingDir -Recurse -Force -ErrorAction SilentlyContinue
    }
}
