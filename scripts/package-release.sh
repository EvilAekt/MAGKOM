#!/usr/bin/env bash
#
# MAGKOM Secure Release Packager (Bash / Linux / CI)
# Mengemas kode rilis tanpa berkas credential, log, backup, dan file lokal sementara.
#
set -euo pipefail

REPO_ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$REPO_ROOT"

TIMESTAMP="$(date +%Y%m%d-%H%M%S)"
DIST_DIR="$REPO_ROOT/dist"
mkdir -p "$DIST_DIR"
OUTPUT_ZIP="${1:-$DIST_DIR/magkom-release-$TIMESTAMP.zip}"

echo "================================================================"
echo "  MAGKOM SECURE RELEASE PACKAGER (BASH / LINUX)                 "
echo "================================================================"
echo "Direktori Sumber : $REPO_ROOT"
echo "Target Arsip ZIP : $OUTPUT_ZIP"

STAGING_DIR="$(mktemp -d /tmp/magkom-pkg-XXXXXX)"
trap 'rm -rf "$STAGING_DIR"' EXIT

echo "1. Menyiapkan berkas rilis ke staging..."
# Gunakan git archive jika repository git tersedia, atau rsync dengan exclusion ketat
if command -v git >/dev/null 2>&1 && [ -d "$REPO_ROOT/.git" ]; then
    git archive --format=tar HEAD | tar -x -C "$STAGING_DIR"
else
    # Fallback rsync dengan filter ketat
    rsync -av --exclude='.git' \
              --exclude='.github' \
              --exclude='.agents' \
              --exclude='.env*' \
              --exclude='.mcp.json' \
              --exclude='scratch' \
              --exclude='graphify-out' \
              --exclude='.strix' \
              --exclude='strix_runs' \
              --exclude='.testsprite' \
              --exclude='testsprite_out' \
              --exclude='node_modules' \
              --exclude='vendor' \
              --exclude='dist' \
              --exclude='storage/logs/*.log' \
              --exclude='storage/app/backups/*' \
              --exclude='storage/app/private/*' \
              --exclude='storage/app/temp/*' \
              --exclude='storage/app/public/profil-peserta/*' \
              --exclude='storage/app/public/foto-staf/*' \
              --exclude='storage/framework/cache/data/*' \
              --exclude='storage/framework/sessions/*' \
              --exclude='storage/framework/views/*' \
              --exclude='tests/check_status.php' \
              --exclude='tests/reset_data.php' \
              "$REPO_ROOT/" "$STAGING_DIR/"
fi

# Pastikan .env.example selalu ada
cp "$REPO_ROOT/.env.example" "$STAGING_DIR/.env.example"

# Pastikan skeleton folder storage ada
mkdir -p "$STAGING_DIR/storage/app/public" \
         "$STAGING_DIR/storage/app/private" \
         "$STAGING_DIR/storage/app/backups" \
         "$STAGING_DIR/storage/framework/cache/data" \
         "$STAGING_DIR/storage/framework/sessions" \
         "$STAGING_DIR/storage/framework/testing" \
         "$STAGING_DIR/storage/framework/views" \
         "$STAGING_DIR/storage/logs" \
         "$STAGING_DIR/bootstrap/cache"

for dir in "$STAGING_DIR"/storage/{app/public,app/private,app/backups,framework/cache/data,framework/sessions,framework/testing,framework/views,logs}; do
    if [ ! -f "$dir/.gitignore" ]; then
        printf "*\n!.gitignore\n" > "$dir/.gitignore"
    fi
done

echo "2. Membuat arsip ZIP..."
rm -f "$OUTPUT_ZIP"
(cd "$STAGING_DIR" && zip -r -q "$OUTPUT_ZIP" .)

echo "3. Memverifikasi isi arsip..."
if unzip -l "$OUTPUT_ZIP" | grep -E "(^|[[:space:]])(\.env|\.env\.local|\.env\.production|\.mcp\.json|storage/app/backups/[^.]|storage/logs/.*\.log)" >/dev/null; then
    echo "ERROR: Kebocoran berkas sensitif terdeteksi di dalam paket ZIP!"
    exit 1
fi

if ! unzip -l "$OUTPUT_ZIP" | grep -E "\.env\.example" >/dev/null; then
    echo "ERROR: .env.example tidak ditemukan di dalam paket ZIP!"
    exit 1
fi

echo "[PASS] Paket rilis aman berhasil diverifikasi: $OUTPUT_ZIP"
